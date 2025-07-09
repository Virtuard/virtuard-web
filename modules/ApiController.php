<?php
namespace Modules;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function __construct()
    {

    }

    public function checkPermission($permission = false)
    {
        if ($permission) {
            if (!Auth::check() or !Auth::user()->hasPermission($permission)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Permission denied',
                    'permission' => $permission
                ], 403);
            }
        }
    }

    public function hasPermission($permission)
    {
        if(!Auth::check()) return response()->json([
            'status' => 'error',
            'message' => 'User not authenticated',
        ], 401);
        return Auth::user()->hasPermission($permission);
    }

    public function checkUserPlanStatus()
    {
         if(!auth()->user()->checkUserPlanStatus() and $row->status == "publish") {
            return response()->json([
                'status' => 'error',
                'message' => 'User plan status is not active',
            ], 403);
        }
    }
}
