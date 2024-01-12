<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ipanorama extends Model
{
    public $incrementing = true;
    protected $table = 'ref_ipanorama';

    protected $fillable = ['slug_product', 'ipanorama'];
    public $timestamps = false;
}
