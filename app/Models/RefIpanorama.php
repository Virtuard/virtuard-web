<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefIpanorama extends Model
{
    public $incrementing = true;
    protected $table = 'ref_add_ipanorama';

    protected $fillable = ['id_user', 'title', 'code'];
    public $timestamps = false;
}
