<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivePoint extends Model
{
    use HasFactory;
        protected $table = 'user_active_points';

protected $fillable = [
        'user_id',
        'day_counter',
        'current_points',
        'first_active_date',
        'last_active_date',
    ];

    /**
     * User relation: har active points record belongs to one user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
