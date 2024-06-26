<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class Ipanorama extends BaseModel
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
    ];

    public function author()
    {
        return $this->belongsTo(User::class, "user_id", "id")->withDefault();
    }
}
