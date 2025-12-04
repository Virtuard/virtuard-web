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
     *                     @OA\Property(property="code", type="string", example="{}"),
     *                     @OA\Property(property="json_data", type="string", example="{}"),
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
        
        // Add scenes field to each panorama
        $panoramas->transform(function ($panorama) {
            $panorama->scenes = $this->extractScenes($panorama);
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
     *             @OA\Property(property="code", type="object", nullable=true, description="Panorama code configuration (JSON object)"),
     *             @OA\Property(property="json_data", type="object", nullable=true, description="Panorama JSON data configuration (JSON object)"),
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

            $ipanorama =  $this->model->create([
                'user_id' => $idUser,
                'create_user' => $idUser,
                'title' => $request->title,
                'code' => $request->has('code') ? (is_string($request->code) ? $request->code : json_encode($request->code)) : null,
                'json_data' => $request->has('json_data') ? (is_string($request->json_data) ? $request->json_data : json_encode($request->json_data)) : null,
                'thumb' => $request->thumb ?? null,
                'status' => $request->status ?? 'draft',
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
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => 'Data failed to save',
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
     *                 @OA\Property(property="code", type="string", example="{}"),
     *                 @OA\Property(property="json_data", type="string", example="{}"),
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
            $panorama->scenes = $this->extractScenes($panorama);

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

            $this->isValidAccess($panorama->user_id);

            $panorama->update($attr);

            return response()->json([
                'status' => true,
                'message' => 'Data updated successfully',
                'data' => $panorama,
            ], 200);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = 'Something went wrong';
            if ($statusCode == 403) {
                $message = 'You do not have permission to access this data';
            }
            return response()->json([
                'status' => false,
                'message' => 'Data failed to update',
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

            $this->isValidAccess($panorama->user_id);

            $panorama->json_data = $jsonData;
            $panorama->save();

            return response()->json([
                'status' => true,
                'message' => 'Data processed successfully',
                'data' => $panorama,
                'attr' => $attr,
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
            $this->isValidAccess($panorama->user_id);

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
            usort($result, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return response()->json([
                'status' => true,
                'message' => 'Files retrieved successfully',
                'data' => $result
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
     * Extract scenes from panorama data
     *
     * @param object $panorama
     * @return array
     */
    protected function extractScenes($panorama)
    {
        $scenes = [];
        
        // Try to extract scenes from code field first
        if (!empty($panorama->code)) {
            $scenes = $this->extractScenesFromCode($panorama);
        }
        
        // If no scenes found in code, try json_data
        if (empty($scenes) && !empty($panorama->json_data)) {
            $scenes = $this->extractScenesFromJsonData($panorama);
        }
        
        return $scenes;
    }

    /**
     * Extract scenes from code field
     *
     * @param object $panorama
     * @return array
     */
    protected function extractScenesFromCode($panorama)
    {
        $scenes = [];
        $code = json_decode($panorama->code);
        
        if (!$code || !isset($code->scenes)) {
            return $scenes;
        }
        
        foreach ($code->scenes as $sceneId => $scene) {
            if (isset($scene->image)) {
                $imageUrl = $this->buildImageUrl($scene->image, $panorama);
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
     * @return array
     */
    protected function extractScenesFromJsonData($panorama)
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
                $imageUrl = $this->buildImageUrl($imageUrl, $panorama);
                $scenes[] = [
                    'id' => is_numeric($index) ? (string)$index : $index,
                    'image' => $imageUrl,
                ];
            }
        }
        
        return $scenes;
    }

    /**
     * Build full URL for image
     *
     * @param string $imageUrl
     * @param object $panorama
     * @return string
     */
    protected function buildImageUrl($imageUrl, $panorama)
    {
        // If already a full URL, normalize and return
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return preg_replace('#([^:])//+#', '$1/', $imageUrl);
        }
        
        // Clean the imageUrl - remove leading/trailing slashes and normalize
        $imageUrl = trim($imageUrl, '/');
        
        // If it's already a full path starting with /uploads/, normalize and return
        if (strpos($imageUrl, 'uploads/') === 0 || strpos($imageUrl, '/uploads/') === 0) {
            $imageUrl = ltrim($imageUrl, '/');
            $cleanPath = '/' . $imageUrl;
            $cleanPath = preg_replace('#/+#', '/', $cleanPath);
            $fullUrl = url($cleanPath);
            return preg_replace('#([^:])//+#', '$1/', $fullUrl);
        }
        
        // Remove common prefixes that might be in the stored path
        $imageUrl = preg_replace('#^(upload/|ipanoramaBuilder/upload/|uploads/ipanoramaBuilder/upload/)#', '', $imageUrl);
        
        // Remove user_id and panorama_id if already in the path
        $pattern = '#^' . preg_quote($panorama->user_id, '#') . '/' . preg_quote($panorama->id, '#') . '/#';
        $imageUrl = preg_replace($pattern, '', $imageUrl);
        
        // Build clean path
        $cleanPath = '/uploads/ipanoramaBuilder/upload/' . $panorama->user_id . '/' . $panorama->id . '/' . $imageUrl;
        
        // Normalize double slashes in path
        $cleanPath = preg_replace('#/+#', '/', $cleanPath);
        
        // Build URL and remove any double slashes (except after http: or https:)
        $fullUrl = url($cleanPath);
        return preg_replace('#([^:])//+#', '$1/', $fullUrl);
    }

    protected function isValidAccess($user_id)
    {
        if (auth()->user()->id !== $user_id) {
            throw new \Exception('You do not have permission to access this data', 403);
        }
    }
}
