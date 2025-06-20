<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginRewardRule extends Model
{
    use HasFactory;
    protected $table = 'login_reward_rule';
    protected $fillable = [
        'day',
        'points',
    ];
}
