<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    public $timestamps = false; // because we are using only created_at manually

    protected $fillable = [
        'user_id',
        'login_at', 
        'is_active',
        'created_at',
    ];

    /**
     * Get the user who owns this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
