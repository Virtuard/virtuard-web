<?php
namespace Modules\Api\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;

class RecentlyController extends Controller{

    public function getRecentlyServices(Request $request)
    {
        $services = DB::table('bravo_services')
            ->select('id', 'title', 'address') 
            ->orderBy('created_at', 'desc') 
            ->take(5)
            ->get(); 

        return response()->json($services);
    }
}