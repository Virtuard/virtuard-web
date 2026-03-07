<?php
namespace Modules\Cultural\Models;

use App\BaseModel;

class CulturalDate extends BaseModel
{
    protected $table = 'bravo_cultural_dates';
    protected $culturalMetaClass;

    protected $casts = [
        'person_types'=>'array'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->culturalMetaClass = CulturalMeta::class;
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
        $meta = $this->culturalMetaClass::where('cultural_date_id', $this->id)->first();
        if (!$meta) {
            $meta = new $this->culturalMetaClass();
            $meta->cultural_date_id = $this->id;
        }
        return $meta->saveMetaOriginOrTranslation($request->input() , $locale);
    }
}
