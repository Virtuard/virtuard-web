<?php
namespace Modules\Boat\Models;

use App\BaseModel;

class BoatCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_boat_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}