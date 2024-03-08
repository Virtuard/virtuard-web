<?php

namespace App\Http\Controllers;

use App\User;
use Modules\Hotel\Models\Hotel;
use Modules\Location\Models\LocationCategory;
use Modules\Page\Models\Page;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\Tag;
use Modules\News\Models\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscribeVirtuard;
use App\Models\ProofSubscribeVirtuard;
use App\Models\RefIpanorama;
use Carbon\Carbon;
use Illuminate\Support\Str;

class VirtuardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function vendorVirtuardIndex()
    {
        $idUser = Auth::id();
        $data = SubscribeVirtuard::where('id_user', $idUser)->get();
        $dataIpanorama = RefIpanorama::where('id_user', $idUser)->get();

        return view('vendor.virtuard360.index', compact('data', 'dataIpanorama'));
    }

    public function vendorVirtuardAdd()
    {
        $idUser = Auth::id();
        $data = SubscribeVirtuard::where('id_user', $idUser)->get();
        // dd($data);

        return view('vendor.virtuard360.add', compact('data'));
    }

    public function vendorVirtuardEdit()
    {
        $idUser = Auth::id();
        $data = SubscribeVirtuard::where('id_user', $idUser)->get();

        return view('vendor.virtuard360.edit', compact('data'));
    }

    public function vendorVirtuardDelete(Request $request)
    {
        $idUser = Auth::id();
        $id = $request->input('id');

        $ipanorama = RefIpanorama::where('id', $id)
            ->where('id_user', $idUser)
            ->first();

        if ($ipanorama) {
            $ipanorama->delete();
            return redirect()->back()->with('success', 'Delete successful');
        } else {
            return redirect()->back()->with('error', 'Record not found or you do not have permission to delete');
        }
    }

    public function adminVirtuardIndex()
    {
        $data = SubscribeVirtuard::join('users', 'subscribe_virtuard.id_user', '=', 'users.id')
            ->select('subscribe_virtuard.*', 'users.name', 'users.email')
            ->get();

        return view('admin.virtuard360.index', compact('data'));
    }

    public function submissionService(Request $request)
    {

        try {
            // Validasi request, pastikan file yang diunggah adalah gambar
            $request->validate([
                'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $dateNow = Carbon::now();

            $proofImage = $request->file('proof');

            $path = $proofImage->store('public/images/proof');

            $idUser = Auth::id();
            $requestService = new SubscribeVirtuard();
            $requestService->id_user = $idUser;
            $requestService->status = "PENDING";
            $requestService->save();

            $subscription = new ProofSubscribeVirtuard();
            $subscription->id_subscribe = $requestService->id;
            $subscription->date = $dateNow;
            $subscription->proof_url = $path;
            $subscription->save();

            return back()->with('success', 'Insert successfully');
        } catch (\Exception $e) {
            // Menangani eksepsi yang mungkin terjadi
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function addNewVirtuard360(Request $request)
    {
        $idUser = Auth::id();

        $ipanorama = new RefIpanorama();
        $ipanorama->id_user = $idUser;
        $ipanorama->title = $request->input('title');
        $ipanorama->save();

        $urlWithId = url('/user/add/virtuard-360?id=' . $ipanorama->id);
        return redirect($urlWithId)->with('success', 'Insert successfully');
    }


    public function addNewImageVirtuard360(Request $request)
    {
        $proofImage = $request->file('image');
        $title = $request->input('title');

        $extension = $proofImage->getClientOriginalExtension();

        $newFileName = Str::slug($title) . '.' . $extension;

        $path = $proofImage->storeAs('/ipanoramaBuilder/upload', $newFileName);

        return back()->with('success', 'Insert successfully');
    }

    public function validateService(Request $request)
    {
        try {
            $param = $request->input('param');
            $id = $request->input('id');

            $dateNow = Carbon::now();
            $dateOneMonthLater = $dateNow->copy()->addMonth(1);

            $subscription = SubscribeVirtuard::where('id', $id)->first();

            if ($subscription) {
                if ($param === 'SUCCESS') {
                    $subscription->status = $param;
                    $subscription->start_date = $dateNow;
                    $subscription->expired_date = $dateOneMonthLater;
                    $subscription->save();
                } else {
                    $subscription->status = $param;
                    $subscription->save();
                }

                return back()->with('success', 'Update successfully');
            } else {
                return back()->with('error', 'Record not found');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function vendorVirtuardAddApi(Request $request)
    {
        $data = json_decode($request->getContent());
        $id = $data->id;

        $virtuard = RefIpanorama::where('id', $id)->first();
        $virtuard->json_data = $data->data;
        $virtuard->save();

        if ($data === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendekode data JSON',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $data,
            'id' => $id,
        ]);
    }

    public function vendorVirtuardAddApiSecond(Request $request)
    {
        $data = $request->except('id');
        $id = $request->id;

        $virtuard = RefIpanorama::find($id);
        $virtuard->update($data);

        if ($data === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendekode data JSON',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $data,
            'id' => $id,
        ]);
    }

    public function vendorVirtuardAjaxGetApi(Request $request)
    {
        // $data = json_decode($request->getContent());
        // $id = $data->id;

        // $virtuard = RefIpanorama::where('id', $id)->first();

        // if ($data === null) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Gagal mendekode data JSON',
        //     ], 400);
        // 

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Data berhasil diproses',
        //     'data' => $virtuard,
        //     'id' => $id,
        // ]);

        $idItem = $request->query('id'); // Mendapatkan nilai 'id' dari parameter query

        // Mencari data berdasarkan 'id'
        $virtuard = RefIpanorama::find($idItem);

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
}
