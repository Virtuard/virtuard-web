<?php
namespace Modules\Natural\Models;

use App\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class NaturalCategory extends BaseModel
{
    use SoftDeletes;
    use NodeTrait;
    protected $table = 'bravo_natural_category';
    protected $fillable = [
        'name',
        'content',
        'slug',
        'status',
        'parent_id'
    ];
    protected $slugField     = 'slug';
    protected $slugFromField = 'name';

    protected $translation_class = NaturalCategoryTranslation::class;

    public static function getModelName()
    {
        return __("Natural Category");
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
        return url(app_get_locale(false, false, '/') . config('natural.natural_route_prefix').'?cat_id[]='.$this->id);
    }

    public static function getLinkForPageSearch($locale = false, $param = [])
    {
        return url(app_get_locale(false, false, '/') . config('natural.natural_route_prefix') . "?" . http_build_query($param));
    }

    public function dataForApi(){
        $translation = $this->translate();
        return [
            'id'=>$this->id,
            'name'=>$translation->name,
            'slug'=>$this->slug,
        ];
    }

    public function natural(){
        return $this->hasMany(Natural::class,'category_id','id');
    }
}
