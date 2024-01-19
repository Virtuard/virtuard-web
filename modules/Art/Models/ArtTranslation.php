<?php

namespace Modules\Art\Models;

use App\BaseModel;

class ArtTranslation extends Art
{
    protected $table = 'bravo_art_translations';

    protected $fillable = [
        'title',
        'content',
        'faqs',
        'address',
        'surrounding'
    ];

    protected $slugField     = false;
    protected $seo_type = 'art_translation';

    protected $cleanFields = [
        'content'
    ];
    protected $casts = [
        'faqs'  => 'array',
        'surrounding'  => 'array',
    ];

    public function getSeoType(){
        return $this->seo_type;
    }
    public function getRecordRoot(){
        return $this->belongsTo(Art::class,'origin_id');

    }
    public static function boot() {
		parent::boot();
		static::saving(function($table)  {
			unset($table->extra_price);
			unset($table->price);
			unset($table->sale_price);
		});
	}
}
