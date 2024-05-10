<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class RefIpanorama extends BaseModel
{
    use SoftDeletes;

    protected $table = 'ref_add_ipanorama';

    protected $fillable = [
        'id_user', 
        'title', 
        'code',
        'json_data',
        'thumb',
        'status',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, "id_user", "id")->withDefault();
    }
}
