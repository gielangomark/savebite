<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'role', // Tambahkan kolom ini
        'google_id',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helper untuk cek role
    public function isMerchant(): bool
    {
        return $this->role === 'merchant';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function wallet() { return $this->morphOne(Wallet::class, 'owner'); }
    public function merchant() { return $this->hasOne(Merchant::class); }

    public function setPhoneAttribute(?string $phone): void
    {
        $this->attributes['phone'] = $phone === null ? null : static::normalizePhone($phone);
    }

    public static function normalizePhone(string $phone): string
    {
        $number = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        return str_starts_with($number, '62') ? $number : '62' . $number;
    }
}
