<?php
namespace Modules\Business\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Business\Hook;
use Modules\Business\Models\BusinessCategory;
use Modules\Business\Models\BusinessCategoryTranslation;

class CategoryController extends AdminController
{
    protected $businessCategoryClass;
    public function __construct()
    {
        $this->setActiveMenu(route('business.admin.index'));
        $this->businessCategoryClass = BusinessCategory::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('business_manage_others');
        $listCategory = $this->businessCategoryClass::query();
        if (!empty($search = $request->query('s'))) {
            $listCategory->where('name', 'LIKE', '%' . $search . '%');
        }
        $listCategory->orderBy('created_at', 'desc');
        $data = [
            'rows'        => $listCategory->get()->toTree(),
            'row'         => new $this->businessCategoryClass(),
            'translation'    => new BusinessCategoryTranslation(),
            'breadcrumbs' => [
                [
                    'name' => __('Business'),
                    'url'  => route('business.admin.index')
                ],
                [
                    'name'  => __('Category'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Business::admin.category.index', $data);
    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('business_manage_others');
        $row = $this->businessCategoryClass::find($id);
        if (empty($row)) {
            return redirect(route('business.admin.category.index'));
        }
        $translation = $row->translate($request->query('lang',get_main_lang()));
        $data = [
            'translation'    => $translation,
            'enable_multi_lang'=>true,
            'row'         => $row,
            'parents'     => $this->businessCategoryClass::get()->toTree(),
            'breadcrumbs' => [
                [
                    'name' => __('Business'),
                    'url'  => route('business.admin.index')
                ],
                [
                    'name'  => __('Category'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Business::admin.category.detail', $data);
    }

    public function store(Request $request , $id)
    {
        $this->checkPermission('business_manage_others');
        $this->validate($request, [
            'name' => 'required'
        ]);
        if($id>0){
            $row = $this->businessCategoryClass::find($id);
            if (empty($row)) {
                return redirect(route('business.admin.category.index'));
            }
        }else{
            $row = new $this->businessCategoryClass();
            $row->status = "publish";
        }

        $row->fill($request->input());
        $res = $row->saveOriginOrTranslation($request->input('lang'),true);

        if ($res) {
            do_action(Hook::AFTER_SAVING_CATEGORY,$row,$request);
            return back()->with('success',  __('Category saved') );
        }
    }

    public function bulkEdit(Request $request)
    {
        $this->checkPermission('business_manage_others');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Select an Action!'));
        }
        if ($action == "delete") {
            foreach ($ids as $id) {
                $query = $this->businessCategoryClass::where("id", $id)->first();
                if(!empty($query)){
                    //Sync child category
                    $list_childs = $this->businessCategoryClass::where("parent_id", $id)->get();
                    if(!empty($list_childs)){
                        foreach ($list_childs as $child){
                            $child->parent_id = null;
                            $child->save();
                        }
                    }
                    //Del parent category
                    $query->delete();
                }
            }
        } else {
            foreach ($ids as $id) {
                $query = $this->businessCategoryClass::where("id", $id);
                $query->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated success!'));
    }

    public function getForSelect2(Request $request)
    {
        $pre_selected = $request->query('pre_selected');
        $selected = $request->query('selected');

        if($pre_selected && $selected){
            $item = $this->businessCategoryClass::find($selected);
            if(empty($item)){
                return response()->json([
                    'text'=>''
                ]);
            }else{
                return response()->json([
                    'text'=>$item->name
                ]);
            }
        }
        $q = $request->query('q');
        $query = $this->businessCategoryClass::select('id', 'name as text')->where("status","publish");
        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get();
        return response()->json([
            'results' => $res
        ]);
    }
}
