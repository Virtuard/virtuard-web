<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use Modules\Hotel\Models\Hotel as ModelsHotel;
use Modules\Media\Models\MediaFile;

class ListingController extends Controller
{
    public function destroyHotels($id)
    {
        $deleted = DB::table('bravo_hotels')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hotel deleted successfully',
        ], 200);
    }

    public function destroySpaces($id)
    {
        $deleted = DB::table('bravo_spaces')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Space not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Space deleted successfully',
        ], 200);
    }

    public function destroyBussiness($id)
    {
        $deleted = DB::table('bravo_businesses')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business deleted successfully',
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:bravo_hotels,slug',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'banner_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->title);

            $userId = Auth::id();
            $tahun = date('Y');
            $bulan = date('m');
            $tanggal = date('d');

            function saveImage($file, $path)
            {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs($path, $filename, 'public');

                    return ['path' => $filePath, 'name' => $filename, 'size' => $file->getSize(), 'mime' => $file->getMimeType()];
                }
                return null;
            }

            $imageData = $request->hasFile('image') ? saveImage($request->file('image'), "uploads/hotel/$userId/$tahun/$bulan/$tanggal") : null;
            $imageId = null;

            if ($imageData) {
                $media = MediaFile::create([
                    'file_name' => $imageData['name'],
                    'file_path' => $imageData['path'],
                    'file_size' => $imageData['size'],
                    'file_type' => $imageData['mime'],
                    'file_extension' => pathinfo($imageData['name'], PATHINFO_EXTENSION),
                    'create_user' => $userId,
                    'update_user' => $userId,
                    'author_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $imageId = $media->id;
            }

            $bannerData = $request->hasFile('banner_image') ? saveImage($request->file('banner_image'), "uploads/hotel/$userId/$tahun/$bulan/$tanggal") : null;
            $bannerImageId = null;

            if ($bannerData) {
                $media = MediaFile::create([
                    'file_name' => $bannerData['name'],
                    'file_path' => $bannerData['path'],
                    'file_size' => $bannerData['size'],
                    'file_type' => $bannerData['mime'],
                    'file_extension' => pathinfo($bannerData['name'], PATHINFO_EXTENSION),
                    'create_user' => $userId,
                    'update_user' => $userId,
                    'author_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $bannerImageId = $media->id;
            }

            $hotel = ModelsHotel::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'image_id' => $imageId,
                'banner_image_id' => $bannerImageId,
                'category_id' => $request->category_id,
                'location_id' => $request->location_id,
                'address' => $request->address,
                'map_lat' => $request->map_lat,
                'map_lng' => $request->map_lng,
                'map_zoom' => $request->map_zoom,
                'gallery' => $request->gallery,
                'video' => $request->video,
                'policy' => $request->policy,
                'star_rate' => $request->star_rate,
                'price' => $request->price,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'status' => $request->status,
                'room' => $request->room,
                'chain' => $request->chain,
                'phone' => $request->phone,
                'website' => $request->website,
                'create_user' => $userId,
                'update_user' => $userId,
                'author_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hotel berhasil ditambahkan',
                'data' => ['hotel_id' => $hotel->id]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


