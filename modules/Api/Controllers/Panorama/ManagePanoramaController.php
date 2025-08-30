<?php

namespace Modules\Api\Controllers\Panorama;

use App\Models\Ipanorama;
use Modules\ApiController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManagePanoramaController extends ApiController
{
    protected $model;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Ipanorama $panorama)
    {
        parent::__construct();
        $this->model = $panorama;
    }

    /**
     * @OA\Post(
     *     path="/api/user/vtour",
     *     tags={"Vtour"},
     *     summary="Create a new vtour",
     *     description="Create a new vtour. Requires vtour_create permission.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Vtour Title", description="Vtour title"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example=true),
     *             @OA\Property(property="message", type="string", example="Data created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="title", type="string", example="Vtour Title"),
     *                 @OA\Property(property="code", type="string", example="{}"),
     *                 @OA\Property(property="json_data", type="string", example="{}"),
     *                 @OA\Property(property="thumb", type="string", example="upload/2/136/250826-136-1-18548.jpg"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="create_user", type="integer", example=5),
     *                 @OA\Property(property="update_user", type="integer", example=5),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User not have plan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not have plan"),
     *             @OA\Property(property="redirect", type="string", example="https://example.com/user/plan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Data failed to create",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data failed to save")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::user()->checkUserIpanoramaPlan()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not have plan',
                    'redirect' => route('user.plan'),
                ], 400);
            }

            $idUser = Auth::id();

            $ipanorama =  $this->model->create([
                'user_id' => $idUser,
                'create_user' => $idUser,
                'title' => $request->title,
                'status' => 'draft',
            ]);

            $urlWithId = route('user.panorama.edit', [
                'id' => $ipanorama->id,
                'user_id' => $idUser,
                'page' => $request->input('page'),
                'wstep' => $request->input('wstep'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data saved successfully',
                'data' => $ipanorama,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data failed to save',
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/user/vtour/{id}",
     *     tags={"Vtour"},
     *     summary="Get a vtour",
     *     description="Get a vtour by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="title", type="string", example="Vtour Title"),
     *                 @OA\Property(property="code", type="string", example="{}"),
     *                 @OA\Property(property="json_data", type="string", example="{}"),
     *                 @OA\Property(property="thumb", type="string", example="upload/2/136/250826-136-1-18548.jpg"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="create_user", type="integer", example=5),
     *                 @OA\Property(property="update_user", type="integer", example=5),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $panorama = $this->model::find($id);

        if (!$panorama) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $panorama,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/user/vtour/{id}",
     *     tags={"Vtour"},
     *     summary="Update a vtour",
     *     description="Update a vtour by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Updated Vtour Title", description="Updated vtour title"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="title", type="string", example="Updated Vtour Title"),
     *                 @OA\Property(property="code", type="string", example="{}"),
     *                 @OA\Property(property="json_data", type="string", example="{}"),
     *                 @OA\Property(property="thumb", type="string", example="upload/2/136/250826-136-1-18548.jpg"),
     *                 @OA\Property(property="status", type="string", example="publish"),
     *                 @OA\Property(property="create_user", type="integer", example=5),
     *                 @OA\Property(property="update_user", type="integer", example=5),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Data failed to process",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data failed to update")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Data failed to process",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data failed to update")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        try {
            $attr = $request->except('id');
            $attr['code']['autoLoad'] = true;

            if ($attr == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data failed to update',
                ], 400);
            }

            $id = $request->id;

            if (!Auth::user()->isAdmin() && Auth::user()->checkUserIpanoramaPlan()) {
                $attr['status'] = 'publish';
            }
            $panorama = $this->model::find($id);
            
            if(auth()->user()->id !== $panorama->user_id){
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to update this data',
                ], 403);
            }

            
            $panorama->update($attr);

            return response()->json([
                'status' => true,
                'message' => 'Data updated successfully',
                'data' => $panorama,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data failed to update',
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/vtour/{id}",
     *     tags={"Vtour"},
     *     summary="Delete a vtour",
     *     description="Delete a vtour by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data not found or you do not have permission to delete")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User does not have permission to delete data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You do not have permission to delete this data")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        $ipanorama = Ipanorama::find($id);

        if ($ipanorama) {
            $ipanorama->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found or you do not have permission to delete',
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/vtour/{id}/load",
     *     tags={"Vtour"},
     *     summary="Load a vtour",
     *     description="Load a vtour by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data loaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data loaded successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="title", type="string", example="Vtour Title"),
     *                 @OA\Property(property="code", type="string", example="{}"),
     *                 @OA\Property(property="json_data", type="string", example="{}"),
     *                 @OA\Property(property="thumb", type="string", example="upload/2/136/250826-136-1-18548.jpg"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="create_user", type="integer", example=5),
     *                 @OA\Property(property="update_user", type="integer", example=5),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Data failed to load",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data failed to load")
     *         )
     *     )
     * )
     */
    public function load(Request $request)
    {
        try {
            $idItem = $request->query('id');

            $virtuard = $this->model::find($idItem);

            if (!$virtuard) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data loaded successfully',
                'data' => $virtuard,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data failed to load',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/vtour/{id}/add-image",
     *     tags={"Vtour"},
     *     summary="Add a new image",
     *     description="Add a new image to a vtour",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"images"},
     *                 @OA\Property(
        *                  property="images",
     *                     type="string",
     *                     format="binary",
     *                     description="The file to upload"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image saved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Data failed to save",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid image format. Only jpeg, png, webp are allowed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Data failed to process",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Image failed to save")
     *         )
     *     )
     * )
     */
    public function addImage(Request $request)
    {
        logger($request->all());
        $request->merge(['user_id' => Auth::id()]);

        $this->validate($request, [
            'images.*' => 'required|mimes:jpeg,png,webp',
        ]);

        try {
            $image = $request->file('images');
            // logger('proofImage: ' . $proofImage);

            // foreach ($proofImage as $image) {
                $path = "/ipanoramaBuilder/upload/" . $request['user_id'] . "/" . $request->id;
                logger('path: ' . $path);
                if ($image) {
                    $newFileName = now()->format('ymd') . '-' . $request->id . '-' . $request->user_id . '-' . $image->getClientOriginalName();
                    $image->storeAs($path, $newFileName);
                }
            // }

            return response()->json([
                'status' => true,
                'message' => 'Image saved successfully',
            ], 201);
        } catch (\Exception $e) {
            logger()->error($e);
            return response()->json([
                'status' => false,
                'message' => 'Image failed to save',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/vtour/{id}/update-json-data",
     *     tags={"Vtour"},
     *     summary="Update JSON data",
     *     description="Update JSON data of a vtour",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"data"},
     *             @OA\Property(property="data", type="string", description="JSON data to update")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data processed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="title", type="string", example="Vtour Title"),
     *                 @OA\Property(property="code", type="string", example="{}"),
     *                 @OA\Property(property="json_data", type="string", example="{}"),
     *                 @OA\Property(property="thumb", type="string", example="upload/2/136/250826-136-1-18548.jpg"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="create_user", type="integer", example=5),
     *                 @OA\Property(property="update_user", type="integer", example=5),
     *             ),
     *             @OA\Property(property="attr", type="object", description="Original request data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Data failed to update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data failed to process")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Data failed to update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data failed to process")
     *         )
     *     )
     * )
     */
    public function updateJsonData(Request $request)
    {
        try {
            $attr = json_decode($request->getContent());

            if ($attr == null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data failed to process',
                ], 400);
            }

            $id = $attr->id;

            $jsonData = json_decode($attr->data);
            $jsonData->config->autoLoad = true;
            $jsonData = json_encode($jsonData);

            $panorama = $this->model::find($id);

            if(auth()->user()->id !== $panorama->user_id){
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to update this data',
                ], 403);
            }

            $panorama->json_data = $jsonData;
            $panorama->save();

            return response()->json([
                'status' => true,
                'message' => 'Data processed successfully',
                'data' => $panorama,
                'attr' => $attr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data failed to process',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/vtour/{id}/get-files",
     *     tags={"Vtour"},
     *     summary="Get files for image selection",
     *     description="Get list of uploaded images for a specific vtour panorama",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", description="Panorama ID")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Files retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Files retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="filename", type="string", example="250826-136-2-18548.jpg"),
     *                     @OA\Property(property="full_path", type="string", example="/uploads/ipanoramaBuilder/upload/2/136/250826-136-2-18548.jpg"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Panorama not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Panorama not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Access denied")
     *         )
     *     )
     * )
     */
    public function getFiles(Request $request, $id)
    {
        try {
            // Validate panorama exists and user has access
            $panorama = $this->model::find($id);
            
            if (!$panorama) {
                return response()->json([
                    'status' => false,
                    'message' => 'Panorama not found'
                ], 404);
            }

            // Check if user has access to this panorama
            $user_id = auth()->user()->id;
            if (!$user_id || $panorama->user_id != $user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Build directory path
            $directory = public_path("uploads/ipanoramaBuilder/upload/{$user_id}/{$id}");
            
            // Check if directory exists
            if (!is_dir($directory)) {
                return response()->json([
                    'status' => true,
                    'message' => 'No files found',
                    'data' => []
                ]);
            }

            $result = [];
            
            // Get all image files from the directory
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            foreach (glob($directory . '/*.*') as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                if (in_array($extension, $allowedExtensions)) {
                    $filename = basename($file);
                    $relativePath = "uploads/ipanoramaBuilder/upload/{$user_id}/{$id}/{$filename}";
                    
                    $result[] = [
                        'filename' => $filename,
                        'full_path' => '/' . $relativePath,
                    ];
                }
            }

            // Sort by creation time (newest first)
            usort($result, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return response()->json([
                'status' => true,
                'message' => 'Files retrieved successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve files: ' . $e->getMessage()
            ], 500);
        }
    }
}
