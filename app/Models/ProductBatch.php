<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $table = 'product_batches'; // optional if you follow Laravel convention

    protected $fillable = [
        'product_id',
        'scan_code',
    ];

   
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
