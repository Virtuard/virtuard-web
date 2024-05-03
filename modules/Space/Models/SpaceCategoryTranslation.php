<?php
namespace Modules\Space\Models;

use App\BaseModel;

class SpaceCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_space_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}