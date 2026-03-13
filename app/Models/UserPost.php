<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPost extends Model
{
    use SoftDeletes;

    protected $table = 'user_post_status';

    protected $fillable = [
        'user_id',
        'ipanorama_id',
        'message',
        'type_status',
        'media',
        'type_post',
        'tag'
    ];
    
    public $timestamps = true;

    public function ipanorama()
    {
        return $this->belongsTo(Ipanorama::class, 'ipanorama_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medias()
    {
        return $this->hasMany(PostMedia::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all tracking records for this post
     */
    public function trackings()
    {
        return $this->hasMany(PuzzleTracking::class, 'post_id');
    }

    /**
     * Get view tracking records
     */
    public function views()
    {
        return $this->hasMany(PuzzleTracking::class, 'post_id')->where('event_type', 'view');
    }

    /**
     * Get play tracking records
     */
    public function plays()
    {
        return $this->hasMany(PuzzleTracking::class, 'post_id')->where('event_type', 'play');
    }

    /**
     * Get screenshot tracking records
     */
    public function screenshots()
    {
        return $this->hasMany(PuzzleTracking::class, 'post_id')->where('event_type', 'screenshot');
    }

    /**
     * Get unique players (users who played)
     */
    public function players()
    {
        return $this->hasMany(PuzzleTracking::class, 'post_id')
            ->where('event_type', 'play')
            ->whereNotNull('user_id')
            ->with('user')
            ->groupBy('user_id');
    }

    /**
     * Get view count
     */
    public function getViewCountAttribute()
    {
        return $this->views()->count();
    }

    /**
     * Get play count
     */
    public function getPlayCountAttribute()
    {
        return $this->plays()->count();
    }

    /**
     * Get unique player count
     */
    public function getUniquePlayerCountAttribute()
    {
        return $this->plays()->whereNotNull('user_id')->distinct()->count('user_id');
    }

    /**
     * Get screenshot count
     */
    public function getScreenshotCountAttribute()
    {
        return $this->screenshots()->count();
    }

    /**
     * Check if post contains puzzleAR link
     */
    public function isPuzzleARPost()
    {
        return $this->message && (stripos($this->message, 'puzzleAR') !== false || stripos($this->message, '/puzzleAR') !== false);
    }

    /**
     * Get the type_post attribute, default to 'public' if empty
     *
     * @param  mixed  $value
     * @return string
     */
    public function getTypePostAttribute($value)
    {
        return empty($value) ? 'public' : $value;
    }
}
