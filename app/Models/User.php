<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'fcm',
        'country',
        'password',
        'image',
        'status',
        'toggle',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }
}
