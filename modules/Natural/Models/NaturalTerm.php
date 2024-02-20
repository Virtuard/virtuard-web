<?php
namespace Modules\Natural\Models;

use App\BaseModel;

class NaturalTerm extends BaseModel
{
    protected $table = 'bravo_natural_term';
    protected $fillable = [
        'term_id',
        'target_id'
    ];
}
