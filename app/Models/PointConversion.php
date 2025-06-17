<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointConversion extends Model
{
    use HasFactory;

    protected $fillable = ['points', 'price'];
    protected $table = 'point_conversions';
}
