<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    public $incrementing = true;
    protected $table = 'user_post_like';

    protected $fillable = ['post_id', 'user_id'];
    public $timestamps = true;
}
