<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ipanorama;
use Illuminate\Support\Str;

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
        ]);
        return redirect($urlWithId)->with('success', 'Insert successfully');
    }


    public function addNewImageVirtuard360(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,png|max:5000',
        ]);

        $proofImage = $request->file('image');
        $title = $request->input('title');

        $path = "/ipanoramaBuilder/upload/" . $request['user_id'];
        if ($proofImage) {
            $extension = $proofImage->getClientOriginalExtension();
    
            $newFileName = Str::slug($title) . '.' . $extension;
    
            $proofImage->storeAs($path, $newFileName);
        }

        return back()->with('success', 'Insert successfully');
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
}
