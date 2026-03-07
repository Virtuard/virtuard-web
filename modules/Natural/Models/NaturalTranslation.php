<?php
namespace Modules\Natural\Models;

use App\BaseModel;

class NaturalTranslation extends BaseModel
{
    protected $table = 'bravo_natural_translations';
    protected $fillable = [
        'title',
        'content',
        'short_desc',
        'address',
        'faqs',
        'include',
        'exclude',
        'itinerary',
        'surrounding',
    ];
    protected $slugField     = false;
    protected $seo_type = 'natural_translation';
    protected $cleanFields = [
        'content'
    ];
    protected $casts = [
        'faqs' => 'array',
        'include' => 'array',
        'exclude' => 'array',
        'itinerary' => 'array',
        'surrounding' => 'array',
    ];
    public function getSeoType(){
        return $this->seo_type;
    }
    public function getRecordRoot(){
        return $this->belongsTo(Natural::class,'origin_id');
    }
}