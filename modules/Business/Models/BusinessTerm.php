<?php
namespace Modules\Business\Models;

use App\BaseModel;

class BusinessTerm extends BaseModel
{
    protected $table = 'bravo_business_term';
    protected $fillable = [
        'term_id',
        'target_id'
    ];
}