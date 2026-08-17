<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'integer',
    ];

    public function product() { return $this->belongsTo(Product::class); }

    public function user() { return $this->belongsTo(User::class); }
}