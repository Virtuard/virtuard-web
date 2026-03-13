<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PuzzleTracking extends BaseModel
{
    use HasFactory;

    protected $table = 'puzzle_tracking';

    protected $fillable = [
        'post_id',
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
        'screenshot_url',
        'metadata',
        'app_installed',
        'user_id',
    ];

    protected $casts = [
        'query_params' => 'array',
        'metadata' => 'array',
        'app_installed' => 'boolean',
    ];

    /**
     * Get the post that this tracking belongs to
     */
    public function post()
    {
        return $this->belongsTo(UserPost::class, 'post_id');
    }

    /**
     * Get the user who triggered this tracking
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Track an event (general method)
     */
    public static function track($eventType, $data = [])
    {
        return static::create(array_merge([
            'event_type' => $eventType,
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
        ], $data));
    }

    /**
     * Track a view event for a post
     */
    public static function trackView($postId, $data = [])
    {
        return static::create(array_merge([
            'post_id' => $postId,
            'event_type' => 'view',
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'platform' => static::detectPlatform(request()->header('User-Agent')),
        ], $data));
    }

    /**
     * Track a play event for a post
     */
    public static function trackPlay($postId, $data = [])
    {
        return static::create(array_merge([
            'post_id' => $postId,
            'event_type' => 'play',
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'platform' => static::detectPlatform(request()->header('User-Agent')),
        ], $data));
    }

    /**
     * Track a screenshot event for a post
     */
    public static function trackScreenshot($postId, $screenshotUrl, $data = [])
    {
        return static::create(array_merge([
            'post_id' => $postId,
            'event_type' => 'screenshot',
            'screenshot_url' => $screenshotUrl,
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'platform' => static::detectPlatform(request()->header('User-Agent')),
        ], $data));
    }

    /**
     * Detect platform from user agent
     */
    private static function detectPlatform($userAgent)
    {
        if (stripos($userAgent, 'android') !== false) {
            return 'android';
        } elseif (stripos($userAgent, 'iphone') !== false || stripos($userAgent, 'ipad') !== false) {
            return 'ios';
        } elseif (stripos($userAgent, 'mobile') !== false) {
            return 'mobile';
        } else {
            return 'web';
        }
    }
}
