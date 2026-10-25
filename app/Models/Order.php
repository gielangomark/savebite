<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'integer',
        'pickup_expires_at' => 'datetime',
        'pickup_warning_sent_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function product() { return $this->belongsTo(Product::class); }

    public function user() { return $this->belongsTo(User::class); }

    public function review()
{
    return $this->hasOne(Review::class);
}
}
