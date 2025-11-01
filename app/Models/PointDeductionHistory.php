<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointDeductionHistory extends Model
{
    use HasFactory;
  	protected $table = 'point_deduction_histories';

	protected $guarded = [];
	
	public function users()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
