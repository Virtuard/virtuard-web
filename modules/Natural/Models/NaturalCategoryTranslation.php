<?php
namespace Modules\Natural\Models;

use App\BaseModel;

class NaturalCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_natural_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}