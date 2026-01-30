<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class GameImage extends Model
{
    use SoftDeletes;

    protected $table = 'game_images';

    protected $fillable = [
        'user_id',
        'url',
    ];

    /**
     * Get the user that owns the game image.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
