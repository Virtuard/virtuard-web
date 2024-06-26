<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPost extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_post_status';

    protected $fillable = ['user_id', 'ipanorama_id', 'message', 'type_status', 'media', 'type_post', 'tag'];
    public $timestamps = true;

    public function ipanorama(){
        return $this->belongsTo(Ipanorama::class, 'ipanorama_id');
    }

    public function user(){
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
}
