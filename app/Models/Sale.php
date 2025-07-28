<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'scan_code',
        'points_earned',
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Product relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
}