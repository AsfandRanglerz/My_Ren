<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\PointDeductionHistory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

	public function pointDeductionHistories()
	{
		return $this->hasMany(PointDeductionHistory::class, 'user_id');
	}
	
}
