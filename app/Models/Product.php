<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'demissions',
        'points_per_sale',
        'image',
    ];
    protected $table = 'products';

    public function batches()
{
    return $this->hasMany(ProductBatch::class, 'product_id');
}

}
