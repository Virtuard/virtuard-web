<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPost extends Model
{
    public $incrementing = true;
    protected $table = 'user_post_status';

    protected $fillable = ['user_id', 'ipanorama_id', 'message', 'type_status', 'media', 'type_post', 'tag'];
    public $timestamps = true;

    public function ipanorama(){
        return $this->belongsTo(RefIpanorama::class, 'ipanorama_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medias()
    {
        return $this->hasMany(PostMedia::class, 'post_id');
    }
}
