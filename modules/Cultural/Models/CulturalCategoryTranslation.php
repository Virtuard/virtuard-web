<?php
namespace Modules\Cultural\Models;

use App\BaseModel;

class CulturalCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_cultural_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}