<?php
namespace Modules\Cultural\Models;

use App\BaseModel;

class CulturalTerm extends BaseModel
{
    protected $table = 'bravo_cultural_term';
    protected $fillable = [
        'term_id',
        'cultural_id'
    ];
}