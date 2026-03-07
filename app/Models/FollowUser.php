<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUser extends Model
{
    public $incrementing = true;
    protected $table = 'follow_member';

    protected $fillable = ['user_id', 'follower_id'];
    public $timestamps = true;

    public function followerUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function followingUser()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
