<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    public $incrementing = true;
    protected $table = 'ref_category_product';

    protected $fillable = ['slug', 'category'];
    public $timestamps = false;
}
