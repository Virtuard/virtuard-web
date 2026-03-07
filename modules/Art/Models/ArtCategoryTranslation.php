<?php
namespace Modules\Art\Models;

use App\BaseModel;

class ArtCategoryTranslation extends BaseModel
{
    protected $table = 'bravo_art_category_translations';
    protected $fillable = [
        'name',
        'content',
    ];
    protected $cleanFields = [
        'content'
    ];
}