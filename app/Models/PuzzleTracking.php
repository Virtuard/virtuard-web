<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PuzzleTracking extends BaseModel
{
    use HasFactory;

    protected $table = 'puzzle_tracking';

    protected $fillable = [
        'session_id',
        'event_type',
        'platform',
        'user_agent',
        'ip_address',
        'referrer',
        'img_url',
        'title',
        'query_params',
        'deep_link_used',
        'redirect_url',
        'app_installed',
        'user_id',
    ];

    protected $casts = [
        'query_params' => 'array',
        'app_installed' => 'boolean',
    ];

    /**
     * Track an event
     */
    public static function track($eventType, $data = [])
    {
        return static::create(array_merge([
            'event_type' => $eventType,
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
        ], $data));
    }
}
