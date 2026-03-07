<?php
namespace Modules\Natural\Models;

use App\BaseModel;

class NaturalDate extends BaseModel
{
    protected $table = 'bravo_natural_dates';
    protected $naturalMetaClass;

    protected $casts = [
        'person_types'=>'array'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->naturalMetaClass = NaturalMeta::class;
    }

    public static function getDatesInRanges($date,$target_id){
        return static::query()->where([
            ['start_date','>=',$date],
            ['end_date','<=',$date],
            ['target_id','=',$target_id],
        ])->first();
    }
    public function saveMeta(\Illuminate\Http\Request $request)
    {
        $locale = $request->input('lang');
        $meta = $this->naturalMetaClass::where('natural_date_id', $this->id)->first();
        if (!$meta) {
            $meta = new $this->naturalMetaClass();
            $meta->natural_date_id = $this->id;
        }
        return $meta->saveMetaOriginOrTranslation($request->input() , $locale);
    }
}
