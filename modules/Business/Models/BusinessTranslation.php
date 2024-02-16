<?php

namespace Modules\Business\Models;

use App\BaseModel;

class BusinessTranslation extends Business
{
    protected $table = 'bravo_business_translations';

    protected $fillable = [
        'title',
        'content',
        'faqs',
        'address',
        'extra_price'
    ];

    protected $slugField     = false;
    protected $seo_type = 'business_translation';

    protected $cleanFields = [
        'content'
    ];
    protected $casts = [
        'faqs'  => 'array',
        'extra_price'  => 'array',
        'surrounding'  => 'array',
    ];

    public function getSeoType(){
        return $this->seo_type;
    }

    public function getRecordRoot(){
        return $this->belongsTo(Business::class,'origin_id');

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