<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    public $incrementing = true;
    protected $table = 'ref_story';

    protected $fillable = ['user_id', 'link', 'link_text', 'media'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
