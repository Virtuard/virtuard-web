<?php
namespace Modules;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscribeVirtuard;

class AdminController extends Controller
{

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
        return Auth::user()->hasPermission($permission);
    }
}
