<?php
namespace App\Services;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class WalletService {
    public function walletFor(Model $owner): Wallet { return Wallet::firstOrCreate(['owner_type' => $owner::class, 'owner_id' => $owner->getKey()]); }
    public function credit(Model $owner, int $amount, string $reference, string $description, ?Model $related = null): WalletTransaction { return $this->record($owner, $amount, $reference, $description, $related, 'credit'); }
    public function debit(Model $owner, int $amount, string $reference, string $description, ?Model $related = null): WalletTransaction { return $this->record($owner, $amount, $reference, $description, $related, 'debit'); }
    private function record(Model $owner, int $amount, string $reference, string $description, ?Model $related, string $type): WalletTransaction {
        return DB::transaction(function () use ($owner, $amount, $reference, $description, $related, $type) {
            $wallet = $this->walletFor($owner); $wallet = Wallet::lockForUpdate()->findOrFail($wallet->id);
            if ($existing = WalletTransaction::where('reference', $reference)->first()) return $existing;
            if ($type === 'debit' && $wallet->balance < $amount) throw ValidationException::withMessages(['wallet' => 'Saldo tidak mencukupi.']);
            $balance = $type === 'credit' ? $wallet->balance + $amount : $wallet->balance - $amount;
            $wallet->update(['balance' => $balance]);
            return WalletTransaction::create(['wallet_id' => $wallet->id, 'type' => $type, 'amount' => $amount, 'balance_after' => $balance, 'reference' => $reference, 'description' => $description, 'related_type' => $related?->getMorphClass(), 'related_id' => $related?->getKey()]);
        });
    }
}
