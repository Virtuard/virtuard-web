<?php
namespace Modules\Cultural\Models;

use App\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class CulturalCategory extends BaseModel
{
    use SoftDeletes;
    use NodeTrait;
    protected $table = 'bravo_cultural_category';
    protected $fillable = [
        'name',
        'content',
        'slug',
        'status',
        'parent_id'
    ];
    protected $slugField     = 'slug';
    protected $slugFromField = 'name';

    protected $translation_class = CulturalCategoryTranslation::class;

    public static function getModelName()
    {
        return __("Cultural Category");
    }

    public static function searchForMenu($q = false)
    {
        $query = static::select('id', 'name');
        if (strlen($q)) {
            $query->where('name', 'like', "%" . $q . "%");
        }
        $a = $query->orderBy('id', 'desc')->limit(10)->get();
        return $a;
    }
    public function getDetailUrl(){
        return url(app_get_locale(false, false, '/') . config('cultural.cultural_route_prefix').'?cat_id[]='.$this->id);
    }

    public static function getLinkForPageSearch($locale = false, $param = [])
    {
        return url(app_get_locale(false, false, '/') . config('cultural.cultural_route_prefix') . "?" . http_build_query($param));
    }

    public function dataForApi(){
        $translation = $this->translate();
        return [
            'id'=>$this->id,
            'name'=>$translation->name,
            'slug'=>$this->slug,
        ];
    }

    public function cultural(){
        return $this->hasMany(Cultural::class,'category_id','id');
    }
}
