<?php
namespace Modules\Art\Models;

use App\BaseModel;

class ArtTerm extends BaseModel
{
    protected $table = 'bravo_art_term';
    protected $fillable = [
        'term_id',
        'target_id'
    ];
}
