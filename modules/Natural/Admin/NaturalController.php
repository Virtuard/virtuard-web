<?php
namespace Modules\Natural\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Core\Events\CreatedServicesEvent;
use Modules\Core\Events\UpdatedServiceEvent;
use Modules\Core\Models\Attributes;
use Modules\Location\Models\LocationCategory;
use Modules\Natural\Hook;
use Modules\Natural\Models\NaturalTerm;
use Modules\Natural\Models\Natural;
use Modules\Natural\Models\NaturalCategory;
use Modules\Natural\Models\NaturalTranslation;
use Modules\Location\Models\Location;
use App\Models\Ipanorama;
use App\Models\CategoryProduct;
use App\Models\RefIpanorama;
use App\Models\RefRelationIpanorama;
use App\Models\SubscribeVirtuard;
use App\Models\ProductCategory;


class NaturalController extends AdminController
{
    protected $naturalClass;
    protected $naturalTranslationClass;
    protected $naturalCategoryClass;
    protected $naturalTermClass;
    protected $attributesClass;
    protected $locationClass;
    /**
     * @var string
     */
    private $locationCategoryClass;

    public function __construct()
    {
        $this->setActiveMenu(route('natural.admin.index'));
        $this->naturalClass = Natural::class;
        $this->naturalTranslationClass = NaturalTranslation::class;
        $this->naturalCategoryClass = NaturalCategory::class;
        $this->naturalTermClass = NaturalTerm::class;
        $this->attributesClass = Attributes::class;
        $this->locationClass = Location::class;
        $this->locationCategoryClass = LocationCategory::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('natural_view');
        $query = $this->naturalClass::query();
        $query->orderBy('id', 'desc');
        if (!empty($natural_name = $request->input('s'))) {
            $query->where('title', 'LIKE', '%' . $natural_name . '%');
            $query->orderBy('title', 'asc');
        }
        if (!empty($cate = $request->input('cate_id'))) {
            $query->where('category_id', $cate);
        }
        if (!empty($is_featured = $request->input('is_featured'))) {
            $query->where('is_featured', 1);
        }
        if (!empty($location_id = $request->query('location_id'))) {
            $query->where('location_id', $location_id);
        }
        if ($this->hasPermission('natural_manage_others')) {
            if (!empty($author = $request->input('vendor_id'))) {
                $query->where('author_id', $author);
            }
        } else {
            $query->where('author_id', Auth::id());
        }
        $data = [
            'rows'               => $query->with([
                'author',
                'category_natural'
            ])->paginate(20),
            'natural_categories'    => $this->naturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'natural_manage_others' => $this->hasPermission('natural_manage_others'),
            'page_title'         => __("Natural Management"),
            'breadcrumbs'        => [
                [
                    'name' => __('Naturals'),
                    'url'  => route('natural.admin.index')
                ],
                [
                    'name'  => __('All'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Natural::admin.index', $data);
    }

    public function recovery(Request $request)
    {
        $this->checkPermission('natural_view');
        $query = $this->naturalClass::onlyTrashed();
        $query->orderBy('id', 'desc');
        if (!empty($natural_name = $request->input('s'))) {
            $query->where('title', 'LIKE', '%' . $natural_name . '%');
            $query->orderBy('title', 'asc');
        }
        if (!empty($cate = $request->input('cate_id'))) {
            $query->where('category_id', $cate);
        }
        if ($this->hasPermission('natural_manage_others')) {
            if (!empty($author = $request->input('vendor_id'))) {
                $query->where('author_id', $author);
            }
        } else {
            $query->where('author_id', Auth::id());
        }
        $data = [
            'rows'               => $query->with([
                'author',
                'category_natural'
            ])->paginate(20),
            'natural_categories'    => $this->naturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'natural_manage_others' => $this->hasPermission('natural_manage_others'),
            'page_title'         => __("Recovery Natural Management"),
            'recovery'           => 1,
            'breadcrumbs'        => [
                [
                    'name' => __('Naturals'),
                    'url'  => route('natural.admin.index')
                ],
                [
                    'name'  => __('Recovery'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Natural::admin.index', $data);
    }

    public function create(Request $request)
    {
        $this->checkPermission('natural_create');
        $row = new Natural();
        $row->fill([
            'status' => 'publish'
        ]);
        $data = [
            'row'               => $row,
            'attributes'        => $this->attributesClass::where('service', 'natural')->get(),
            'natural_category'     => $this->naturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'natural_location'     => $this->locationClass::where('status', 'publish')->get()->toTree(),
            'location_category' => $this->locationCategoryClass::where("status", "publish")->get(),
            'translation'       => new $this->naturalTranslationClass(),
            'breadcrumbs'       => [
                [
                    'name' => __('Naturals'),
                    'url'  => route('natural.admin.index')
                ],
                [
                    'name'  => __('Add Natural'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Natural::admin.detail', $data);

    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('natural_update');
        $row = $this->naturalClass::find($id);
        if (empty($row)) {
            return redirect(route('natural.admin.index'));
        }
        $translation = $row->translate($request->query('lang',get_main_lang()));
        if (!$this->hasPermission('natural_manage_others')) {
            if ($row->author_id != Auth::id()) {
                return redirect(route('natural.admin.index'));
            }
        }
        $data = [
            'row'               => $row,
            'translation'       => $translation,
            "selected_terms"    => $row->natural_term->pluck('term_id'),
            'attributes'        => $this->attributesClass::where('service', 'natural')->get(),
            'natural_category'     => $this->naturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'natural_location'     => $this->locationClass::where('status', 'publish')->get()->toTree(),
            'location_category' => $this->locationCategoryClass::where("status", "publish")->get(),
            'enable_multi_lang' => true,
            'breadcrumbs'       => [
                [
                    'name' => __('Naturals'),
                    'url'  => route('natural.admin.index')
                ],
                [
                    'name'  => __('Edit Natural'),
                    'class' => 'active'
                ],
            ],
            'page_title'=>__('Edit Natural')
        ];
        return view('Natural::admin.detail', $data);
    }

    public function store(Request $request, $id)
    {

        if ($id > 0) {
            $this->checkPermission('natural_update');
            $row = $this->naturalClass::find($id);
            if (empty($row)) {
                return redirect(route('natural.admin.index'));
            }
            if ($row->author_id != Auth::id() and !$this->hasPermission('natural_manage_others')) {
                return redirect(route('natural.admin.index'));
            }
        } else {
            $this->checkPermission('natural_create');
            $row = new $this->naturalClass();
            $row->status = "publish";
        }
        if(!empty($request->input('enable_fixed_date'))){
            $rules = [
                'start_date'        =>'required|date',
                'end_date'         =>'required|date|after_or_equal:start_date',
                'last_booking_date' =>'required|date|before:start_date|after:'.now(),
            ];
            $request->validate($rules);
        }

        $row->fill($request->input());
        if ($request->input('slug')) {
            $row->slug = $request->input('slug');
        }

        $row->ical_import_url = $request->ical_import_url;
        $row->author_id = $request->input('author_id');
        $row->default_state = $request->input('default_state', 1);
        $row->enable_service_fee = $request->input('enable_service_fee');
        $row->service_fee = $request->input('service_fee');
        $res = $row->saveOriginOrTranslation($request->input('lang'), true);

        if ($res) {
            if (!$request->input('lang') or is_default_lang($request->input('lang'))) {
                $this->saveTerms($row, $request);
                $row->saveMeta($request);
            }

            do_action(Hook::AFTER_SAVING,$row,$request);

            if ($id > 0) {
                event(new UpdatedServiceEvent($row));

                $ipanoramaInp = RefRelationIpanorama::where('slug', '=', $row->slug)->first();

                if ($ipanoramaInp) {
                    $ipanoramaInp->id_ipanorama = $request->input('div-ipanorama');
                    $ipanoramaInp->save();
                } else {
                    $ipanoramaInpNew = new RefRelationIpanorama();
                    $ipanoramaInpNew->id_ipanorama = $request->input('div-ipanorama');
                    $ipanoramaInpNew->slug = $row->slug;
                    $ipanoramaInpNew->save();
                }

                return back()->with('success', __('Natural updated'));
            } else {
                event(new CreatedServicesEvent($row));

                $ipanoramaInp = new RefRelationIpanorama();
                $ipanoramaInp->id_ipanorama = $request->input('div-ipanorama');
                $ipanoramaInp->slug = $row->slug;
                $ipanoramaInp->save();

                return redirect(route('natural.admin.edit', $row->id))->with('success', __('Natural created'));
            }
        }
    }

    public function saveTerms($row, $request)
    {
        if (empty($request->input('terms'))) {
            $this->naturalTermClass::where('natural_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                $this->naturalTermClass::firstOrCreate([
                    'term_id' => $term_id,
                    'natural_id' => $row->id
                ]);
            }
            $this->naturalTermClass::where('natural_id', $row->id)->whereNotIn('term_id', $term_ids)->delete();
        }
    }

    public function bulkEdit(Request $request)
    {

        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('No items selected!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Please select an action!'));
        }
        switch ($action) {
            case "delete":
                foreach ($ids as $id) {
                    $query = $this->naturalClass::where("id", $id);
                    if (!$this->hasPermission('natural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('natural_delete');
                    }
                    $row = $query->first();
                    if (!empty($row)) {
                        $row->delete();
                        event(new UpdatedServiceEvent($row));
                    }
                }
                return redirect()->back()->with('success', __('Deleted success!'));
                break;
            case "permanently_delete":
                foreach ($ids as $id) {
                    $query = $this->naturalClass::where("id", $id);
                    if (!$this->hasPermission('natural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('natural_delete');
                    }
                    $row = $query->withTrashed()->first();
                    if ($row) {
                        $row->forceDelete();
                    }
                }
                return redirect()->back()->with('success', __('Permanently delete success!'));
                break;
            case "recovery":
                foreach ($ids as $id) {
                    $query = $this->naturalClass::withTrashed()->where("id", $id);
                    if (!$this->hasPermission('natural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('natural_delete');
                    }
                    $row = $query->first();
                    if (!empty($row)) {
                        $row->restore();
                        event(new UpdatedServiceEvent($row));
                    }
                }
                return redirect()->back()->with('success', __('Recovery success!'));
                break;
            case "clone":
                $this->checkPermission('natural_create');
                foreach ($ids as $id) {
                    (new $this->naturalClass())->saveCloneByID($id);
                }
                return redirect()->back()->with('success', __('Clone success!'));
                break;
            default:
                // Change status
                foreach ($ids as $id) {
                    $query = $this->naturalClass::where("id", $id);
                    if (!$this->hasPermission('natural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('natural_update');
                    }
                    $row = $query->first();
                    $row->status = $action;
                    $row->save();
                    event(new UpdatedServiceEvent($row));
                }
                return redirect()->back()->with('success', __('Update success!'));
                break;
        }
    }

    public function getForSelect2(Request $request)
    {
        $pre_selected = $request->query('pre_selected');
        $selected = $request->query('selected');
        if ($pre_selected && $selected) {
            if (is_array($selected)) {
                $items = $this->naturalClass::select('id', 'title as text')->whereIn('id', $selected)->take(50)->get();
                return $this->sendSuccess([
                    'items' => $items
                ]);
            } else {
                $item = $this->naturalClass::find($selected);
            }
            if (empty($item)) {
                return $this->sendSuccess([
                    'text' => ''
                ]);
            } else {
                return $this->sendSuccess([
                    'text' => $item->name
                ]);
            }
        }
        $q = $request->query('q');
        $query = $this->naturalClass::select('id', 'title as text')->where("status", "publish");
        if ($q) {
            $query->where('title', 'like', '%' . $q . '%');
        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get();
        return $this->sendSuccess([
            'results' => $res
        ]);
    }
}
