<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'otp',
        'otp_token',
        'verified',
        'country',
        'expires_at',
    ];



    
}
