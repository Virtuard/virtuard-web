<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PuzzleConfig extends BaseModel
{
    use HasFactory;

    protected $table = 'puzzle_config';

    protected $fillable = [
        'android_package',
        'android_store_link',
        'android_deep_link_scheme',
        'ios_app_id',
        'ios_store_link',
        'ios_deep_link_scheme',
        'web_game_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active configuration
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }
}
