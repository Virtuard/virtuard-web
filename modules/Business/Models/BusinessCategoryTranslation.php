<?php
namespace Modules\Business\Models;

use App\BaseModel;

class BusinessCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_business_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}