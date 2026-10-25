<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model {
    protected $guarded = [];
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function products() { return $this->hasMany(Product::class); }
    public function wallet() { return $this->morphOne(Wallet::class, 'owner'); }
    public function withdrawals() { return $this->hasMany(Withdrawal::class); }

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function whatsappNumber(): string
    {
        $number = preg_replace('/\D+/', '', $this->whatsapp ?? '');

        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        return str_starts_with($number, '62') ? $number : '62' . $number;
    }
}
