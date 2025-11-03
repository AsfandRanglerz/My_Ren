<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPointDeductionHistory extends Model
{
    use HasFactory;
	protected $table = 'temp_point_deduction_histories';
	protected $guarded = [];
}
