<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    protected $table = 'user_post_media';

    protected $fillable = [
        'post_id',
        'media',
        'type',
    ];
}
