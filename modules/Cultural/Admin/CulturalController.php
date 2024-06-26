<?php
namespace Modules\Cultural\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Core\Events\CreatedServicesEvent;
use Modules\Core\Events\UpdatedServiceEvent;
use Modules\Core\Models\Attributes;
use Modules\Location\Models\LocationCategory;
use Modules\Cultural\Hook;
use Modules\Cultural\Models\CulturalTerm;
use Modules\Cultural\Models\Cultural;
use Modules\Cultural\Models\CulturalCategory;
use Modules\Cultural\Models\CulturalTranslation;
use Modules\Location\Models\Location;

class CulturalController extends AdminController
{
    protected $culturalClass;
    protected $culturalTranslationClass;
    protected $culturalCategoryClass;
    protected $culturalTermClass;
    protected $attributesClass;
    protected $locationClass;
    /**
     * @var string
     */
    private $locationCategoryClass;

    public function __construct()
    {
        $this->setActiveMenu(route('cultural.admin.index'));
        $this->culturalClass = Cultural::class;
        $this->culturalTranslationClass = CulturalTranslation::class;
        $this->culturalCategoryClass = CulturalCategory::class;
        $this->culturalTermClass = CulturalTerm::class;
        $this->attributesClass = Attributes::class;
        $this->locationClass = Location::class;
        $this->locationCategoryClass = LocationCategory::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('cultural_view');
        $query = $this->culturalClass::query();
        $query->orderBy('id', 'desc');
        if (!empty($cultural_name = $request->input('s'))) {
            $query->where('title', 'LIKE', '%' . $cultural_name . '%');
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
        if ($this->hasPermission('cultural_manage_others')) {
            if (!empty($author = $request->input('vendor_id'))) {
                $query->where('author_id', $author);
            }
        } else {
            $query->where('author_id', Auth::id());
        }
        $data = [
            'rows'               => $query->with([
                'author',
                'category_cultural'
            ])->paginate(20),
            'cultural_categories'    => $this->culturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'cultural_manage_others' => $this->hasPermission('cultural_manage_others'),
            'page_title'         => __("Cultural Management"),
            'breadcrumbs'        => [
                [
                    'name' => __('Culturals'),
                    'url'  => route('cultural.admin.index')
                ],
                [
                    'name'  => __('All'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Cultural::admin.index', $data);
    }

    public function recovery(Request $request)
    {
        $this->checkPermission('cultural_view');
        $query = $this->culturalClass::onlyTrashed();
        $query->orderBy('id', 'desc');
        if (!empty($cultural_name = $request->input('s'))) {
            $query->where('title', 'LIKE', '%' . $cultural_name . '%');
            $query->orderBy('title', 'asc');
        }
        if (!empty($cate = $request->input('cate_id'))) {
            $query->where('category_id', $cate);
        }
        if ($this->hasPermission('cultural_manage_others')) {
            if (!empty($author = $request->input('vendor_id'))) {
                $query->where('author_id', $author);
            }
        } else {
            $query->where('author_id', Auth::id());
        }
        $data = [
            'rows'               => $query->with([
                'author',
                'category_cultural'
            ])->paginate(20),
            'cultural_categories'    => $this->culturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'cultural_manage_others' => $this->hasPermission('cultural_manage_others'),
            'page_title'         => __("Recovery Cultural Management"),
            'recovery'           => 1,
            'breadcrumbs'        => [
                [
                    'name' => __('Culturals'),
                    'url'  => route('cultural.admin.index')
                ],
                [
                    'name'  => __('Recovery'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Cultural::admin.index', $data);
    }

    public function create(Request $request)
    {
        $this->checkPermission('cultural_create');
        $row = new Cultural();
        $row->fill([
            'status' => 'publish'
        ]);
        $data = [
            'row'               => $row,
            'attributes'        => $this->attributesClass::where('service', 'cultural')->get(),
            'cultural_category'     => $this->culturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'cultural_location'     => $this->locationClass::where('status', 'publish')->get()->toTree(),
            'location_category' => $this->locationCategoryClass::where("status", "publish")->get(),
            'translation'       => new $this->culturalTranslationClass(),
            'breadcrumbs'       => [
                [
                    'name' => __('Culturals'),
                    'url'  => route('cultural.admin.index')
                ],
                [
                    'name'  => __('Add Cultural'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Cultural::admin.detail', $data);

    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('cultural_update');
        $row = $this->culturalClass::find($id);
        if (empty($row)) {
            return redirect(route('cultural.admin.index'));
        }
        $translation = $row->translate($request->query('lang',get_main_lang()));
        if (!$this->hasPermission('cultural_manage_others')) {
            if ($row->author_id != Auth::id()) {
                return redirect(route('cultural.admin.index'));
            }
        }
        $data = [
            'row'               => $row,
            'translation'       => $translation,
            "selected_terms"    => $row->cultural_term->pluck('term_id'),
            'attributes'        => $this->attributesClass::where('service', 'cultural')->get(),
            'cultural_category'     => $this->culturalCategoryClass::where('status', 'publish')->get()->toTree(),
            'cultural_location'     => $this->locationClass::where('status', 'publish')->get()->toTree(),
            'location_category' => $this->locationCategoryClass::where("status", "publish")->get(),
            'enable_multi_lang' => true,
            'breadcrumbs'       => [
                [
                    'name' => __('Culturals'),
                    'url'  => route('cultural.admin.index')
                ],
                [
                    'name'  => __('Edit Cultural'),
                    'class' => 'active'
                ],
            ],
            'page_title'=>__('Edit Cultural')
        ];
        return view('Cultural::admin.detail', $data);
    }

    public function store(Request $request, $id)
    {

        if ($id > 0) {
            $this->checkPermission('cultural_update');
            $row = $this->culturalClass::find($id);
            if (empty($row)) {
                return redirect(route('cultural.admin.index'));
            }
            if ($row->author_id != Auth::id() and !$this->hasPermission('cultural_manage_others')) {
                return redirect(route('cultural.admin.index'));
            }
        } else {
            $this->checkPermission('cultural_create');
            $row = new $this->culturalClass();
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
                return back()->with('success', __('Cultural updated'));
            } else {
                event(new CreatedServicesEvent($row));
                return redirect(route('cultural.admin.edit', $row->id))->with('success', __('Cultural created'));
            }
        }
    }

    public function saveTerms($row, $request)
    {
        if (empty($request->input('terms'))) {
            $this->culturalTermClass::where('cultural_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                $this->culturalTermClass::firstOrCreate([
                    'term_id' => $term_id,
                    'cultural_id' => $row->id
                ]);
            }
            $this->culturalTermClass::where('cultural_id', $row->id)->whereNotIn('term_id', $term_ids)->delete();
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
                    $query = $this->culturalClass::where("id", $id);
                    if (!$this->hasPermission('cultural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('cultural_delete');
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
                    $query = $this->culturalClass::where("id", $id);
                    if (!$this->hasPermission('cultural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('cultural_delete');
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
                    $query = $this->culturalClass::withTrashed()->where("id", $id);
                    if (!$this->hasPermission('cultural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('cultural_delete');
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
                $this->checkPermission('cultural_create');
                foreach ($ids as $id) {
                    (new $this->culturalClass())->saveCloneByID($id);
                }
                return redirect()->back()->with('success', __('Clone success!'));
                break;
            default:
                // Change status
                foreach ($ids as $id) {
                    $query = $this->culturalClass::where("id", $id);
                    if (!$this->hasPermission('cultural_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('cultural_update');
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
                $items = $this->culturalClass::select('id', 'title as text')->whereIn('id', $selected)->take(50)->get();
                return $this->sendSuccess([
                    'items' => $items
                ]);
            } else {
                $item = $this->culturalClass::find($selected);
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
        $query = $this->culturalClass::select('id', 'title as text')->where("status", "publish");
        if ($q) {
            $query->where('title', 'like', '%' . $q . '%');
        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get();
        return $this->sendSuccess([
            'results' => $res
        ]);
    }
}
