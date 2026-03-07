<?php
namespace Modules\Natural\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Natural\Models\Natural;
use Modules\Natural\Models\NaturalCategory;

class BookingController extends AdminController
{
    protected $naturalClass;
    public function __construct()
    {
        $this->setActiveMenu(route('natural.admin.index'));
        $this->naturalClass = Natural::class;
    }

    public function index(Request $request){

        $this->checkPermission('natural_create');

        $q = $this->naturalClass::query();

        if($request->query('s')){
            $q->where('title','like','%'.$request->query('s').'%');
        }

        if ($cat_id = $request->query('cat_id')) {
            $cat = NaturalCategory::find($cat_id);
            if(!empty($cat)) {
                $q->join('bravo_natural_category', function ($join) use ($cat) {
                    $join->on('bravo_natural_category.id', '=', 'bravo_naturals.category_id')
                        ->where('bravo_natural_category._lft','>=',$cat->_lft)
                        ->where('bravo_natural_category._rgt','>=',$cat->_lft);
                });
            }
        }

        if(!$this->hasPermission('natural_manage_others')){
            $q->where('author_id',$this->currentUser()->id);
        }

        $q->orderBy('bravo_naturals.id','desc');

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

        $prev_url = route('natural.admin.booking.index',array_merge($request->query(),[
           'month'=> date('m-Y',$current_month - MONTH_IN_SECONDS)
        ]));
        $next_url = route('natural.admin.booking.index',array_merge($request->query(),[
           'month'=> date('m-Y',$current_month + MONTH_IN_SECONDS)
        ]));

        $natural_categories = NaturalCategory::where('status', 'publish')->get()->toTree();
        $breadcrumbs = [
            [
                'name' => __('Naturals'),
                'url'  => route('natural.admin.index')
            ],
            [
                'name'  => __('Booking'),
                'class' => 'active'
            ],
        ];
        $page_title = __('Natural Booking History');
        return view('Natural::admin.booking.index',compact('rows','natural_categories','breadcrumbs','current_month','page_title','request','prev_url','next_url'));
    }
    public function test(){
        $d = new \DateTime('2019-07-04 00:00:00');

        $d->modify('+ 4 hours');
        echo $d->format('Y-m-d H:i:s');
    }
}
