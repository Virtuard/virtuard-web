<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUser extends Model
{
    public $incrementing = true;
    protected $table = 'follow_member';

    protected $fillable = ['user_id', 'follow_user_id'];
    public $timestamps = true;
}
