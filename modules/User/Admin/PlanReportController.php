<?php
namespace Modules\User\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\User\Models\Plan;
use Modules\User\Models\UserPlan;

class PlanReportController extends AdminController
{
    protected $planClass;
    protected $userPlanClass;
    public function __construct()
    {
        $this->setActiveMenu(route('user.admin.plan.index'));
        $this->userPlanClass = UserPlan::class;
        $this->planClass = Plan::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('dashboard_access');
        $rows = $this->userPlanClass::query();
        if (!empty($plan_id = $request->query('plan_id'))) {
            $rows->where('plan_id', $plan_id);
        }
        if (!empty($create_user = $request->query('create_user'))) {
            $rows->where('user_id', $create_user);
        }
        $rows->with(['user','plan'])->orderBy('id', 'desc');
        $data = [
            'rows'        => $rows->paginate(20),
            'plans' => $this->planClass::where('status','publish')->get(),
            'breadcrumbs' => [
                [
                    'name'  => __('User Plans'),
                    'class' => 'active'
                ],
            ],
            'page_title'=>__("Plan Report")
        ];
        return view('User::admin.plan-report.index', $data);
    }

    public function bulkEdit(Request $request)
    {
        $this->checkPermission('dashboard_access');
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
                $query = $this->planClass::where("id", $id)->first();
                if(!empty($query)){
                    //Del parent category
                    $query->delete();
                }
            }
        } else {
            foreach ($ids as $id) {
                $query = $this->planClass::where("id", $id);
                $query->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated success!'));
    }

    public function create(Request $request)
    {
        $this->checkPermission('dashboard_access');
        $row = new $this->userPlanClass();

        $data = [
            'row' => $row,
            'plans' => $this->planClass::where('status', 'publish')->get(),
            'breadcrumbs'  => [
                [
                    'name' => __('Plan Report'),
                    'url'  => route('user.admin.plan_report.index')
                ],
                [
                    'name'  => __('Add Plan'),
                    'class' => 'active'
                ],
            ],
            'page_title'   => __("Add new Plan"),

        ];
        return view('User::admin.plan-report.detail', $data);
    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('dashboard_access');
        $row = $this->userPlanClass::find($id);
        if (empty($row)) {
            return redirect(route('user.admin.plan_report.index'));
        }
        $data = [
            'row' => $row,
            'plans' => $this->planClass::where('status', 'publish')->get(),
            'breadcrumbs' => [
                [
                    'name' => __('Plan Report'),
                    'url'  => route('user.admin.plan_report.index')
                ],
                [
                    'name'  => __('Edit Plan'),
                    'class' => 'active'
                ],
            ],
            'page_title'        => __("Edit: :name", ['name' => 'Plan'])
        ];
        return view('User::admin.plan-report.detail', $data);
    }

    public function store(Request $request, $id)
    {
        if (is_demo_mode()) {
            return back()->with("error", "DEMO MODE: You are not allowed to change data");
        }

        if ($id > 0) {
            $this->checkPermission('dashboard_access');
            $row = $this->userPlanClass::find($id);
            if (empty($row)) {
                return redirect(route('user.admin.plan_report.index'));
            }
        } else {
            $this->checkPermission('dashboard_access');
            $row = new $this->userPlanClass();
        }

        $plan = $this->planClass::query()->find($request->plan_id);

        $user_plan = $this->userPlanClass::query()->find($id);
        if (empty($user_plan)) {
            $user_plan = new $this->userPlanClass();
        }
        $user_plan->plan_id = $request->plan_id;
        $user_plan->price = $plan->price;
        $user_plan->start_date = date('Y-m-d H:i:s');
        $user_plan->max_service = $plan->max_service;
        $user_plan->max_ipanorama = $plan->max_ipanorama;
        $user_plan->plan_data = $plan;
        $user_plan->user_id = $request->author_id;
        $user_plan->status = $request->status ?? 0;

        if ($id > 0) {
            $user_plan->end_date = $request->end_date;
        } else {
            if ($plan->duration) {
                $user_plan->end_date = date('Y-m-d H:i:s', strtotime('+ ' . $plan->duration . ' ' . $plan->duration_type));
            }
        }

        $user_plan->save();

        if ($user_plan) {
            if ($id > 0) {
                return back()->with('success', __('Plan updated'));
            } else {
                return redirect(route('user.admin.plan_report.edit', $user_plan->id))->with('success', __('Plan created'));
            }
        }
    }
}
