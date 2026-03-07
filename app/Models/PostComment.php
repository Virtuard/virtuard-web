<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    public $incrementing = true;
    protected $table = 'user_post_comment';

    protected $fillable = ['post_id', 'user_id', 'comment'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
