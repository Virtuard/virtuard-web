<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    public $incrementing = true;
    protected $table = 'product_category';

    protected $fillable = ['type', 'title'];
    public $timestamps = false;
}
