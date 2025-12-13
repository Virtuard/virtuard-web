<?php

namespace Modules\Api\Controllers\Panorama;

use App\Models\Ipanorama;
use Modules\ApiController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
     * @OA\Get(
     *     path="/api/user/vtour",
     *     tags={"Vtour"},
     *     summary="Get all panoramas",
     *     description="Get a list of all panoramas for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="title", type="string", example="Vtour Title"),
     *                     @OA\Property(property="code", type="object", nullable=true, example={"theme":"ipnrm-theme-default","autoLoad":true}),
     *                     @OA\Property(property="json_data", type="object", nullable=true, example={"id":"ipanorama_config__ipanorama_config","name":"ipanorama_config","config":{"theme":"ipnrm-theme-default","autoLoad":true}}),
     *                     @OA\Property(property="thumb", type="string", nullable=true, example="upload/2/136/250826-136-1-18548.jpg"),
     *                     @OA\Property(property="status", type="string", example="draft"),
     *                     @OA\Property(property="create_user", type="integer", example=5),
     *                     @OA\Property(property="update_user", type="integer", nullable=true, example=5),
     *                     @OA\Property(property="scenes", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="string", example="main"),
     *                             @OA\Property(property="image", type="string", format="url", example="http://example.com/uploads/ipanoramaBuilder/upload/5/1/scene-image.jpg")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $panoramas = $this->model->all();
        
        // Add scenes field to each panorama and decode JSON fields
        $panoramas->transform(function ($panorama) {
            $panorama->scenes = $this->extractScenes($panorama, true); // Use relative path for index
            $this->decodeJsonFields($panorama);
            return $panorama;
        });
        
        return response()->json([
            'status' => true,
            'data' => $panoramas,
        ])->setEncodingOptions(JSON_UNESCAPED_SLASHES);
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
     *             @OA\Property(
     *                 property="code",
     *                 type="object",
     *                 nullable=true,
     *                 description="Panorama code configuration (JSON object) containing autoLoad and scenes properties",
     *                 @OA\Property(property="theme", type="string", example="ipnrm-theme-default", description="Panorama theme"),
     *                 @OA\Property(property="autoLoad", type="boolean", example=true, description="Auto load panorama"),
     *                 @OA\Property(property="autoRotate", type="boolean", example=false, description="Enable auto rotate"),
     *                 @OA\Property(property="grab", type="boolean", example=true, description="Enable grab/drag interaction"),
     *                 @OA\Property(property="sceneId", type="string", example="scene1", description="Default scene ID"),
     *                 @OA\Property(
     *                     property="scenes",
     *                     type="object",
     *                     description="Scene definitions (key-value pairs where key is scene ID)",
     *                     @OA\AdditionalProperties(
     *                         type="object",
     *                         @OA\Property(property="type", type="string", example="sphere", description="Scene type: sphere, cube, or cylinder"),
     *                         @OA\Property(property="image", type="string", example="upload/2/148/251204-148-2-create.jpeg", description="Scene image path"),
     *                         @OA\Property(property="saveCamera", type="boolean", example=true, description="Save camera position"),
     *                         @OA\Property(property="title", type="string", nullable=true, example="Room", description="Scene title"),
     *                         @OA\Property(
     *                             property="hotSpots",
     *                             type="array",
     *                             description="Array of hotspots",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="title", type="string", example="Room", description="Hotspot title"),
     *                                 @OA\Property(property="yaw", type="number", format="float", example=0, description="Hotspot yaw position"),
     *                                 @OA\Property(property="pitch", type="number", format="float", example=0, description="Hotspot pitch position"),
     *                                 @OA\Property(property="sceneId", type="string", nullable=true, example="scene2", description="Link to another scene ID"),
     *                                 @OA\Property(property="popoverHtml", type="boolean", example=true, description="Use HTML for popover"),
     *                                 @OA\Property(property="popoverShow", type="boolean", example=false, description="Show popover on load")
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="json_data",
     *                 type="object",
     *                 nullable=true,
     *                 description="Panorama JSON data configuration containing config with scenes array",
     *                 @OA\Property(property="id", type="string", example="ipanorama_config__ipanorama_config", description="Configuration ID"),
     *                 @OA\Property(property="name", type="string", example="ipanorama_config", description="Configuration name"),
     *                 @OA\Property(
     *                     property="config",
     *                     type="object",
     *                     @OA\Property(property="theme", type="string", example="ipnrm-theme-default", description="Panorama theme"),
     *                     @OA\Property(property="autoLoad", type="boolean", example=true, description="Auto load panorama"),
     *                     @OA\Property(
     *                         property="scenes",
     *                         type="array",
     *                         description="Array of scene configurations",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="string", example="Scene 1", description="Scene ID"),
     *                             @OA\Property(property="title", type="string", nullable=true, example="Room", description="Scene title"),
     *                             @OA\Property(
     *                                 property="config",
     *                                 type="object",
     *                                 @OA\Property(property="type", type="string", example="sphere", description="Scene type"),
     *                                 @OA\Property(
     *                                     property="imageFront",
     *                                     type="object",
     *                                     @OA\Property(property="isCustom", type="boolean", example=false, description="Is custom image"),
     *                                     @OA\Property(property="url", type="string", example="251204-148-2-create.jpeg", description="Front image filename")
     *                                 ),
     *                                 @OA\Property(property="yaw", type="number", format="float", example=0, description="Starting yaw position"),
     *                                 @OA\Property(property="pitch", type="number", format="float", example=0, description="Starting pitch position"),
     *                                 @OA\Property(property="zoom", type="number", format="float", example=75, description="Starting zoom level"),
     *                                 @OA\Property(
     *                                     property="hotspots",
     *                                     type="array",
     *                                     description="Array of hotspots",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="string", example="Hotspot 1", description="Hotspot ID"),
     *                                         @OA\Property(
     *                                             property="config",
     *                                             type="object",
     *                                             @OA\Property(property="title", type="string", example="Room", description="Hotspot title"),
     *                                             @OA\Property(property="yaw", type="number", format="float", example=0, description="Hotspot yaw position"),
     *                                             @OA\Property(property="pitch", type="number", format="float", example=0, description="Hotspot pitch position"),
     *                                             @OA\Property(property="sceneId", type="string", nullable=true, example="scene2", description="Link to another scene ID")
     *                                         )
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="thumb", type="string", nullable=true, maxLength=255, example="upload/2/136/250826-136-1-18548.jpg", description="Thumbnail image path"),
     *             @OA\Property(property="status", type="string", nullable=true, example="draft", description="Panorama status (draft or publish)"),
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

            $code = $request->code ? json_encode($request->code) : null;
            $json_data = $request->json_data ? json_encode($request->json_data) : null;

            $data = [
                'user_id' => $idUser,
                'create_user' => $idUser,
                'title' => $request->title,
                'code' => $code,
                'json_data' => $json_data,
                'thumb' => $request->thumb ?? null,
                'status' => $request->status ?? 'draft',
            ];

            $ipanorama =  $this->model->create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data saved successfully',
                'data' => $ipanorama,
            ], 201);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => $message,
            ], $statusCode);
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
     *                 @OA\Property(property="code", type="object", nullable=true, example={"theme":"ipnrm-theme-default","autoLoad":true}),
     *                 @OA\Property(property="json_data", type="object", nullable=true, example={"id":"ipanorama_config__ipanorama_config","name":"ipanorama_config","config":{"theme":"ipnrm-theme-default","autoLoad":true}}),
     *                 @OA\Property(property="thumb", type="string", example="upload/2/136/250826-136-1-18548.jpg"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="create_user", type="integer", example=5),
     *                 @OA\Property(property="update_user", type="integer", example=5),
     *                 @OA\Property(property="scenes", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="main"),
     *                         @OA\Property(property="image", type="string", format="url", example="http://example.com/uploads/ipanoramaBuilder/upload/5/1/scene-image.jpg")
     *                     )
     *                 )
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
        try {
            $panorama = $this->model::find($id);

            if (!$panorama) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                ], 404);
            }

            // $this->isValidAccess($panorama->user_id);

            // Add scenes field to panorama
            $panorama->scenes = $this->extractScenes($panorama, true); // Use relative path for show
            
            // Decode JSON fields to objects
            $this->decodeJsonFields($panorama);

            return response()->json([
                'status' => true,
                'data' => $panorama,
            ])->setEncodingOptions(JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => $message
            ], $statusCode);
        }
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
     *             @OA\Property(property="title", type="string", nullable=true, maxLength=255, example="Updated Vtour Title", description="Vtour title"),
     *             @OA\Property(property="code", type="object", nullable=true, description="Panorama code configuration (JSON object)"),
     *             @OA\Property(property="json_data", type="object", nullable=true, description="Panorama JSON data configuration (JSON object)"),
     *             @OA\Property(property="thumb", type="string", nullable=true, maxLength=255, example="upload/2/136/250826-136-1-18548.jpg", description="Thumbnail image path"),
     *             @OA\Property(property="status", type="string", nullable=true, example="publish", description="Panorama status (draft or publish)"),
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
    public function update(Request $request, $id)
    {
        try {
            $panorama = $this->model->find($id);

            $this->isValidAccess($panorama->user_id);

            $data = $request->all();
            
            if ($request['status'] == 'publish') {
                if (!Auth::user()->checkUserIpanoramaPlan()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User not have plan',
                        'redirect' => route('user.plan'),
                    ], 400);
                }
            }

            $panorama->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Data updated successfully',
                'data' => $panorama,
            ], 200);
        } catch (\Exception $e) {
            logger($e);
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => $message,
            ], $statusCode);
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
     *             @OA\Property(property="message", type="string", example="Delete data failed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User does not have permission to delete data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Delete data failed")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $ipanorama = Ipanorama::find($id);

            if (!$ipanorama) {
                return response()->json([
                    'status' => false,
                    'message' => 'Delete data failed',
                ], 404);
            }

            $this->isValidAccess($ipanorama->user_id);

            $ipanorama->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully',
            ]);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => $message
            ], $statusCode);
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
    public function addImage(Request $request, $id)
    {
        $request->merge(['user_id' => Auth::id()]);

        // Validate images
        if (!$request->hasFile('images')) {
            return response()->json([
                'status' => false,
                'message' => 'No image file provided',
            ], 400);
        }

        $images = $request->file('images');

        // Validate single file or array of files
        if (is_array($images)) {
            foreach ($images as $image) {
                if (!$image || !$image->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid image file provided',
                    ], 400);
                }

                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid file type. Only JPEG, PNG, and WebP are allowed.',
                    ], 400);
                }

                if ($image->getSize() > 10 * 1024 * 1024) { // 10MB
                    return response()->json([
                        'status' => false,
                        'message' => 'File size too large. Maximum size is 10MB.',
                    ], 400);
                }
            }
        } else {
            // Single file validation
            if (!$images->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid image file provided',
                ], 400);
            }

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($images->getMimeType(), $allowedMimes)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid file type. Only JPEG, PNG, and WebP are allowed.',
                ], 400);
            }

            if ($images->getSize() > 10 * 1024 * 1024) { // 10MB
                return response()->json([
                    'status' => false,
                    'message' => 'File size too large. Maximum size is 10MB.',
                ], 400);
            }
        }

        try {
            $images = $request->file('images');

            // Handle both single file and multiple files
            $imageArray = is_array($images) ? $images : [$images];

            foreach ($imageArray as $image) {
                if (!$image || !$image->isValid()) {
                    continue;
                }

                $path = "ipanoramaBuilder/upload/" . $request['user_id'] . "/" . $id;

                $newFileName = now()->format('ymd') . '-' . $id . '-' . $request->user_id . '-' . $image->getClientOriginalName();

                // Ensure directory exists
                $fullPath = public_path('uploads/' . $path);
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                // Store file using uploads disk
                $image->storeAs($path, $newFileName, 'uploads');
            }

            return response()->json([
                'status' => true,
                'message' => 'Image saved successfully',
            ], 201);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => $message
            ], $statusCode);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/vtour/get-files/{user_id}",
     *     tags={"Vtour"},
     *     summary="Get files for image selection",
     *     description="Get list of uploaded images organized by directory and root files for a specific user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", description="User ID")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Files retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Files retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     oneOf={
     *                         @OA\Schema(
     *                             type="object",
     *                             @OA\Property(property="type", type="string", example="directory"),
     *                             @OA\Property(property="name", type="string", example="147"),
     *                             @OA\Property(property="files", type="array",
     *                                 @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="type", type="string", example="file"),
     *                                     @OA\Property(property="name", type="string", example="251020-147-2-4.png")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Schema(
     *                             type="object",
     *                             @OA\Property(property="type", type="string", example="file"),
     *                             @OA\Property(property="name", type="string", example="boat.jpg")
     *                         )
     *                     }
     *                 ),
     *                 example={
     *                     {
     *                         "type": "directory",
     *                         "name": "147",
     *                         "files": {
     *                             {
     *                                 "type": "file",
     *                                 "name": "251020-147-2-4.png"
     *                             },
     *                             {
     *                                 "type": "file",
     *                                 "name": "251020-147-2-5.png"
     *                             }
     *                         }
     *                     },
     *                     {
     *                         "type": "file",
     *                         "name": "boat.jpg"
     *                     }
     *                 }
     *             )
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
    public function getFiles(Request $request, $user_id)
    {
        try {
            $result = [];
            $directory = public_path("uploads/ipanoramaBuilder/upload/{$user_id}");
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (File::exists($directory)) {
                $folders = File::directories($directory);

                foreach ($folders as $folderPath) {
                    $folderName = basename($folderPath);
                    $folderFiles = [];

                    $files = File::files($folderPath);

                    foreach ($files as $file) {
                        $extension = strtolower($file->getExtension());
                        
                        if (in_array($extension, $allowedExtensions)) {
                            $folderFiles[] = [
                                'type' => 'file',
                                'name' => $file->getFilename()
                            ];
                        }
                    }

                    if (!empty($folderFiles)) {
                        $result[] = [
                            'type' => 'directory',
                            'name' => $folderName,
                            'files' => $folderFiles
                        ];
                    }
                }

                $rootFiles = File::files($directory);

                foreach ($rootFiles as $file) {
                    $extension = strtolower($file->getExtension());
                    
                    if (in_array($extension, $allowedExtensions)) {
                        $result[] = [
                            'type' => 'file',
                            'name' => $file->getFilename()
                        ];
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Files retrieved successfully',
                'data' => $result
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => $message
            ], $statusCode);
        }
    }

    /**
     * Extract scenes from panorama data
     *
     * @param object $panorama
     * @param bool $useRelativePath Whether to use relative path (from uploads) instead of full URL
     * @return array
     */
    protected function extractScenes($panorama, $useRelativePath = false)
    {
        $scenes = [];
        
        // Try to extract scenes from code field first
        if (!empty($panorama->code)) {
            $scenes = $this->extractScenesFromCode($panorama, $useRelativePath);
        }
        
        // If no scenes found in code, try json_data
        if (empty($scenes) && !empty($panorama->json_data)) {
            $scenes = $this->extractScenesFromJsonData($panorama, $useRelativePath);
        }
        
        return $scenes;
    }

    /**
     * Extract scenes from code field
     *
     * @param object $panorama
     * @param bool $useRelativePath Whether to use relative path (from uploads) instead of full URL
     * @return array
     */
    protected function extractScenesFromCode($panorama, $useRelativePath = false)
    {
        $scenes = [];
        $code = json_decode($panorama->code);
        
        if (!$code || !isset($code->scenes)) {
            return $scenes;
        }
        
        foreach ($code->scenes as $sceneId => $scene) {
            if (isset($scene->image)) {
                $imageUrl = $this->buildImagePath($scene->image, $panorama);
                $scenes[] = [
                    'id' => $sceneId,
                    'image' => $imageUrl,
                ];
            }
        }
        
        return $scenes;
    }

    /**
     * Extract scenes from json_data field
     *
     * @param object $panorama
     * @param bool $useRelativePath Whether to use relative path (from uploads) instead of full URL
     * @return array
     */
    protected function extractScenesFromJsonData($panorama, $useRelativePath = false)
    {
        $scenes = [];
        $jsonData = json_decode($panorama->json_data);
        
        if (!$jsonData || !isset($jsonData->config->scenes)) {
            return $scenes;
        }
        
        $scenesArray = is_array($jsonData->config->scenes) 
            ? $jsonData->config->scenes 
            : (array) $jsonData->config->scenes;
        
        foreach ($scenesArray as $index => $scene) {
            $imageUrl = null;
            
            // Try different possible image paths
            if (isset($scene->image)) {
                $imageUrl = $scene->image;
            } elseif (isset($scene->config->imageFront->url)) {
                $imageUrl = $scene->config->imageFront->url;
            }
            
            if ($imageUrl) {
                $imageUrl = $this->buildImagePath($imageUrl, $panorama);
                $scenes[] = [
                    'id' => is_numeric($index) ? (string)$index : $index,
                    'image' => $imageUrl,
                ];
            }
        }
        
        return $scenes;
    }

    /**
     * Decode JSON fields from string to object
     *
     * @param object $panorama
     * @return void
     */
    protected function decodeJsonFields($panorama)
    {
        if (!empty($panorama->code) && is_string($panorama->code)) {
            $decoded = json_decode($panorama->code);
            $panorama->code = $decoded !== null ? $decoded : $panorama->code;
        }
        
        if (!empty($panorama->json_data) && is_string($panorama->json_data)) {
            $decoded = json_decode($panorama->json_data);
            $panorama->json_data = $decoded !== null ? $decoded : $panorama->json_data;
        }
    }

    /**
     * Build relative path for image (from uploads)
     *
     * @param string $imageUrl
     * @param object $panorama
     * @return string
     */
    protected function buildImagePath($imageUrl, $panorama)
    {
        // If already a full URL, extract the path
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $parsedUrl = parse_url($imageUrl);
            $path = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '';
            // If path starts with uploads/, return it
            if (strpos($path, 'uploads/') === 0) {
                return $path;
            }
        }
        
        // Clean the imageUrl - remove leading/trailing slashes and normalize
        $imageUrl = trim($imageUrl, '/');
        
        // If it's already a path starting with uploads/, normalize and return
        if (strpos($imageUrl, 'uploads/') === 0) {
            return preg_replace('#/+#', '/', $imageUrl);
        }
        
        // Remove common prefixes that might be in the stored path
        $imageUrl = preg_replace('#^(upload/|ipanoramaBuilder/upload/|uploads/ipanoramaBuilder/upload/)#', '', $imageUrl);
        
        // Remove user_id and panorama_id if already in the path
        $pattern = '#^' . preg_quote($panorama->user_id, '#') . '/' . preg_quote($panorama->id, '#') . '/#';
        $imageUrl = preg_replace($pattern, '', $imageUrl);
        $pattern = '#^' . preg_quote($panorama->user_id, '#') . '/#';
        $imageUrl = preg_replace($pattern, '', $imageUrl);
        
        // Step 1: Try uploads/ipanoramaBuilder/imageUrl
        $path1 = 'uploads/ipanoramaBuilder/' . $imageUrl;
        $fullPath1 = public_path($path1);
        if (File::exists($fullPath1)) {
            return preg_replace('#/+#', '/', $path1);
        }
        
        // Step 2: Try uploads/ipanoramaBuilder/user_id/imageUrl
        $path2 = 'uploads/ipanoramaBuilder/' . $panorama->user_id . '/' . $imageUrl;
        $fullPath2 = public_path($path2);
        if (File::exists($fullPath2)) {
            return preg_replace('#/+#', '/', $path2);
        }
        
        // Step 3: Use uploads/ipanoramaBuilder/user_id/panorama_id/imageUrl
        $path3 = 'uploads/ipanoramaBuilder/' . $panorama->user_id . '/' . $panorama->id . '/' . $imageUrl;
        return preg_replace('#/+#', '/', $path3);
    }


    protected function isValidAccess($user_id)
    {
        if (auth()->user()->isAdmin() || auth()->user()->id === $user_id) {
            return true;
        }
        
        throw new \Exception('You do not have permission to access this data', 403);
    }
}
