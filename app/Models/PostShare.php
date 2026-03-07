<?php

namespace App\Models;

use App\BaseModel;
use App\Models\User;

class PostShare extends BaseModel
{
    protected $table = 'post_shares';

    protected $fillable = [
        'post_id',
        'user_id'
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
