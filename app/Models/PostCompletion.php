<?php

namespace App\Models;

use App\BaseModel;
use App\Models\User;

class PostCompletion extends BaseModel
{
    protected $table = 'post_completions';

    protected $fillable = [
        'post_id',
        'user_id',
        'time_spent',
        'moves'
    ];

    public function post()
    {
        return $this->belongsTo(UserPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
