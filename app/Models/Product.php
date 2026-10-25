<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];
    
    public function merchant() { 
        return $this->belongsTo(Merchant::class); 
    }
    public function reviews()
        {
            return $this->hasMany(Review::class);
        }

        public function averageRating(): float
        {
            return (float) round($this->reviews()->avg('rating') ?? 0, 1);
        }

        public function review()
    {
        return $this->hasOne(Review::class);
    }
}
