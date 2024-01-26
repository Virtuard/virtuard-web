<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefIpanorama extends Model
{
    public $incrementing = true;
    protected $table = 'ref_add_ipanorama';
    public $timestamps = false;

    protected $fillable = [
        'id_user', 
        'title', 
        'code',
        'json_data',
        'thumb',
    ];
}
