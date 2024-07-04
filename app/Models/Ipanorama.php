<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class Ipanorama extends Model
{
    use SoftDeletes;

    protected $table = 'ipanoramas';

    protected $fillable = [
        'user_id', 
        'title', 
        'code',
        'json_data',
        'thumb',
        'status',
        'create_user',
        'update_user',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, "user_id", "id")->withDefault();
    }
}
