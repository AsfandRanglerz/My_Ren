<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivePointsHistory extends Model
{
    use HasFactory;

    protected $table = 'user_activepoints_history'; // Table name as you defined

    protected $fillable = [
        'user_id',
        'points_awarded',
        'source',
        'day_counter',
        'remarks',
    ];

    /**
     * User relation: Every history entry belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
