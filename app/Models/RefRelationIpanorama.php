<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefRelationIpanorama extends Model
{
    public $incrementing = true;
    protected $table = 'ref_relation_ipanorama';

    protected $fillable = ['id_ipanorama', 'slug'];
    public $timestamps = false;
}
