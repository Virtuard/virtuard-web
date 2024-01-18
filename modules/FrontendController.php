<?php
namespace Modules;

use App\Http\Controllers\Controller;
use App\Models\SubscribeVirtuard;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function __construct()
    {

    }

    public function checkPermission($permission = false)
    {
        if ($permission) {
            if (!Auth::check() or !Auth::user()->hasPermission($permission)) {
                abort(403);
            }
        }
    }

    public function checkVirtuard360()
    {
        $idUser = Auth::id();

        $virtuard360 = SubscribeVirtuard::where('id_user', $idUser)->get();

        $isVirtuard360 = false;

        if(!empty($virtuard360) && isset($virtuard360[0])) $isVirtuard360 = true;

        return $isVirtuard360;
    }

    public function hasPermission($permission)
    {
        if(!Auth::check()) return false;
        return Auth::user()->hasPermission($permission);
    }
}
