<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class UserGameProgress extends Model
{
    use SoftDeletes;

    protected $table = 'user_game_progress';

    protected $fillable = [
        'user_id',
        'current_level',
        'current_stage',
        'current_checkpoint',
        'total_score',
        'experience',
        'coins',
        'lives',
        'completed_levels_data',
        'puzzle_details',
        'trophies',
        'total_play_time',
        'create_user',
        'update_user',
    ];

    protected $casts = [
        'completed_levels_data' => 'array',
        'puzzle_details' => 'array',
        'trophies' => 'array',
        'current_level' => 'integer',
        'current_stage' => 'integer',
        'current_checkpoint' => 'integer',
        'total_score' => 'integer',
        'experience' => 'integer',
        'coins' => 'integer',
        'lives' => 'integer',
        'total_play_time' => 'integer',
    ];

    /**
     * Get the user that owns the game progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
