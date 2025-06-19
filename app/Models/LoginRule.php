<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'Consecutive-days',
        'points',
    ];
    
    protected $table = 'login_rules'; 
}
