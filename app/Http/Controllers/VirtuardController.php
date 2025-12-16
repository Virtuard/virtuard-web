<?php

namespace App\Http\Controllers;

use App\Models\Ipanorama;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class VirtuardController extends Controller
{
    protected $panorama;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->panorama = new Ipanorama();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function vendorVirtuardIndex()
    {
        $idUser = Auth::id();
        $dataIpanorama = Ipanorama::where('user_id', $idUser)->get();

        return view('user.virtuard360.index', compact('dataIpanorama'));
    }

    public function vendorVirtuardAdd(Request $request)
    {
        return view('user.virtuard360.add');
    }

    public function show($id)
    {
        $panorama = Ipanorama::find($id);

        $data = [
            'panorama' => $panorama,
        ];

        return view('user.virtuard360.show', $data);
    }

    public function vendorVirtuardEdit(Request $request)
    {
        if($request->id){
            $panorama = Ipanorama::find($request->id);
        }

        $data = [
            'panorama' => $panorama,
            'user_id' => auth()->user()->id,
        ];

        return view('user.virtuard360.edit', $data);
    }

    public function vendorVirtuardDelete($id)
    {
        $ipanorama = Ipanorama::find($id);

        if ($ipanorama) {
            $ipanorama->delete();
            return redirect()->back()->with('success', 'Delete successful');
        } else {
            return redirect()->back()->with('error', 'Record not found or you do not have permission to delete');
        }
    }

    public function addNewVirtuard360(Request $request)
    {
        if (!auth()->user()->checkUserIpanoramaPlan()) {
            return redirect(route('user.plan'));
        }

        $idUser = Auth::id();

        $ipanorama = new Ipanorama();
        $ipanorama->user_id = $idUser;
        $ipanorama->create_user = $idUser;
        $ipanorama->title = $request->input('title');
        $ipanorama->status = 'draft';
        $ipanorama->save();

        $urlWithId = route('user.virtuard-360.edit', [
            'id' => $ipanorama->id,
            'user_id' => $idUser,
            'page' => $request->input('page'),
            'wstep' => $request->input('wstep'),
        ]);
        return redirect($urlWithId)->with('success', 'Insert successfully');
    }


    public function addNewImageVirtuard360(Request $request)
    {
        $this->validate($request, [
            'images.*' => 'required|mimes:jpeg,png,webp',
        ]);

        $proofImage = $request->file('images');
        $title = $request->input('title');

        foreach($proofImage as $image){
            $path = "/ipanoramaBuilder/upload/" . $request['user_id'] . "/" . $request->panorama_id;
            if ($image) {
                // $extension = $image->getClientOriginalExtension();
                $newFileName = now()->format('ymd') . '-' . $request->panorama_id . '-' . $request->user_id . '-' . $image->getClientOriginalName();
                // $filePath = $path . '/' . $newFileName;
                
                // if ($extension == 'webp') {
                    $image->storeAs($path, $newFileName);
                // } else {
                //     $newFileName = Str::slug($title) . '.webp';
                //     $filePath = $path . '/' . $newFileName;
                    
                //     $img = Image::make($proofImage);
                //     $img->save(Storage::disk('uploads')->path($filePath));
                // }

            }
        }
        
        return redirect()->route('user.virtuard-360.edit', [
            'id' => $request->panorama_id, 
            'user_id' => $request->user_id,
            'page' => $request->page ? $request->page : '',
            'wstep' => $request->wstep ? $request->wstep+1 : ''
        ])->with('success', 'Insert successfully');
    }

    public function vendorVirtuardAddApi(Request $request)
    {
        $attr = json_decode($request->getContent());

        if ($attr == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal diproses',
            ], 400);
        }
        
        $id = $attr->id;

        $jsonData = json_decode($attr->data);
        $jsonData->config->autoLoad = true;
        $jsonData = json_encode($jsonData);

        $panorama = Ipanorama::find($id);

        abort_if(auth()->user()->id !== $panorama->user_id, 403);

        $panorama->json_data = $jsonData;
        $panorama->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $attr,
            'id' => $id,
        ]);
    }

    public function vendorVirtuardAddApiSecond(Request $request)
    {
        $attr = $request->except('id');
        $attr['code']['autoLoad'] = true;

        if ($attr == null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal diproses',
            ], 400);
        }

        $id = $request->id;
        $panorama = Ipanorama::find($id);
        
        abort_if(auth()->user()->id !== $panorama->user_id, 403);

        if(!auth()->user()->isAdmin() && auth()->user()->checkUserIpanoramaPlan()) {
            $attr['status'] = 'publish';
        }
        $panorama->update($attr);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $attr,
            'id' => $id,
        ]);
    }

    public function vendorVirtuardAjaxGetApi(Request $request)
    {
        $idItem = $request->query('id'); // Mendapatkan nilai 'id' dari parameter query

        // Mencari data berdasarkan 'id'
        $virtuard = Ipanorama::find($idItem);

        if (!$virtuard) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $virtuard,
        ]);
    }

    public function updateIsTourField()
    {
        $user = Auth::user();

        if ($user instanceof \App\Models\User) {
            $user->update(['is_tour' => 1]);
            return response()->json(['message' => 'Tour field updated successfully']);
        } else {
            return response()->json(['error' => 'User model not found or not using the expected Eloquent model']);
        }
    }

    public function bulkEdit($id , Request $request){
        $action = $request->input('action');
        $user_id = Auth::id();
        $query = $this->panorama::where("user_id", $user_id)->where("id", $id)->first();
        if (empty($id)) {
            return redirect()->back()->with('error', __('No item!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Please select an action!'));
        }
        if(empty($query)){
            return redirect()->back()->with('error', __('Not Found'));
        }
        switch ($action){
            case "make-hide":
                $query->status = "draft";
                break;
            case "make-publish":
                if(!auth()->user()->checkUserIpanoramaPlan()) {
                    return redirect(route('user.plan'));
                }
                $query->status = "publish";
                break;
        }
        $query->save();

        return redirect()->back()->with('success', __('Update success!'));
    }

    public function compressPanorama(Request $request, $id)
    {
        $is_replace = $request['is_replace'] ?? false;

        try {
            $panorama = $this->panorama->find($id);
            $compress = compress_view_panorama($panorama, $is_replace);

            return response()->json([
                'status' => 'success',
                'data' => $compress,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
            ]);
        }
    }

    public function share($id)
    {
        $panorama = Ipanorama::where([
            'uuid' => $id,
            'status' => 'publish',
        ])->first();

        $listingUrl = '';
        if($panorama->hotels->count() > 0){
            $listingUrl = route('hotel.detail', $panorama->hotels[0]->slug);
        }elseif($panorama->spaces->count() > 0){
            $listingUrl = route('space.detail', $panorama->spaces[0]->slug);
        }elseif($panorama->businesses->count() > 0){
            $listingUrl = route('business.detail', $panorama->businesses[0]->slug);
        }
        
        abort_if(!$panorama, 404);

        if(!$panorama->author->checkUserPlanStatus()) {
            return redirect(route('plan.expired'));
        }

        $data = [
            'panorama' => $panorama,
            'listingUrl' => $listingUrl,
        ];

        return view('app.panorama.share', $data);
    }

    public function preview($id)
    {
        $panorama = Ipanorama::where([
            'uuid' => $id,
            'status' => 'publish',
        ])->first();
        abort_if(!$panorama, 404);

        if(!$panorama->author->checkUserPlanStatus()) {
            return redirect(route('plan.expired'));
        }

        $embedUrl = route('panorama.share', $panorama->uuid);

        $data = [
            'panorama' => $panorama,
            'embedUrl' => $embedUrl,
        ];

        return view('app.panorama.preview', $data);
    }

    public function expiredPlan()
    {
        return view('app.panorama.expired');
    }
}
