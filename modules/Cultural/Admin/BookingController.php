<?php
namespace Modules\Cultural\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Cultural\Models\Cultural;
use Modules\Cultural\Models\CulturalCategory;

class BookingController extends AdminController
{
    protected $culturalClass;
    public function __construct()
    {
        $this->setActiveMenu(route('cultural.admin.index'));
        $this->culturalClass = Cultural::class;
    }

    public function index(Request $request){

        $this->checkPermission('cultural_create');

        $q = $this->culturalClass::query();

        if($request->query('s')){
            $q->where('title','like','%'.$request->query('s').'%');
        }

        if ($cat_id = $request->query('cat_id')) {
            $cat = CulturalCategory::find($cat_id);
            if(!empty($cat)) {
                $q->join('bravo_cultural_category', function ($join) use ($cat) {
                    $join->on('bravo_cultural_category.id', '=', 'bravo_culturals.category_id')
                        ->where('bravo_cultural_category._lft','>=',$cat->_lft)
                        ->where('bravo_cultural_category._rgt','>=',$cat->_lft);
                });
            }
        }

        if(!$this->hasPermission('cultural_manage_others')){
            $q->where('author_id',$this->currentUser()->id);
        }

        $q->orderBy('bravo_culturals.id','desc');

        $rows = $q->paginate(10);

        $current_month = time();

        if($request->query('month')){
            $date = date_create_from_format('m-Y',$request->query('month'));
            if(!$date){
                $current_month = time();
            }else{
                $current_month = $date->getTimestamp();
            }
        }

        $prev_url = route('cultural.admin.booking.index',array_merge($request->query(),[
           'month'=> date('m-Y',$current_month - MONTH_IN_SECONDS)
        ]));
        $next_url = route('cultural.admin.booking.index',array_merge($request->query(),[
           'month'=> date('m-Y',$current_month + MONTH_IN_SECONDS)
        ]));

        $cultural_categories = CulturalCategory::where('status', 'publish')->get()->toTree();
        $breadcrumbs = [
            [
                'name' => __('Culturals'),
                'url'  => route('Cultural.admin.index')
            ],
            [
                'name'  => __('Booking'),
                'class' => 'active'
            ],
        ];
        $page_title = __('Cultural Booking History');
        return view('Cultural::admin.booking.index',compact('rows','cultural_categories','breadcrumbs','current_month','page_title','request','prev_url','next_url'));
    }
    public function test(){
        $d = new \DateTime('2019-07-04 00:00:00');

        $d->modify('+ 4 hours');
        echo $d->format('Y-m-d H:i:s');
    }
}
