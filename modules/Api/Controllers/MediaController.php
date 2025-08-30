<?php
namespace Modules\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Media\Models\MediaFile;
use Modules\Media\Resources\MediaResource;
class MediaController extends \Modules\Media\Admin\MediaController
{
    /**
     * Upload media file
     * 
     * @OA\Post(
     *     path="/api/media/upload",
     *     summary="Upload a media file",
     *     description="Upload an image, video, or document file to the media library",
     *     tags={"Media"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="The file to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     default="image",
     *                     description="File type (image, video, document, etc.)"
     *                 ),
     *                 @OA\Property(
     *                     property="folder_id",
     *                     type="integer",
     *                     description="ID of the folder to store the file in"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="ckeditor",
     *         in="query",
     *         description="Set to true if uploading for CKEditor",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Uploaded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="uploaded", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="file_name", type="string", example="image.jpg"),
     *                     @OA\Property(property="file_path", type="string", example="uploads/2024/01/image.jpg"),
     *                     @OA\Property(property="file_size", type="integer", example=1024000),
     *                     @OA\Property(property="file_type", type="string", example="image/jpeg")
     *                 ),
     *                 @OA\Property(property="fileName", type="string", example="image.jpg"),
     *                 @OA\Property(property="url", type="string", example="https://example.com/uploads/2024/01/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Please log in"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="uploaded", type="integer", example=0))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - No upload permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="There is no permission upload"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="uploaded", type="integer", example=0))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or upload failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Upload failed"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="uploaded", type="integer", example=0))
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if(!\auth()->user()){
            return $this->sendError(__("Please log in"));
        }

        $ckEditor = $request->query('ckeditor');

        if (!$this->hasPermissionMedia()) {
            return $this->sendError('There is no permission upload');
        }
        $fileName = 'file';
        if($ckEditor) $fileName = 'upload';

        try{
            $file_type = $request->input('type','image');

            $fileObj = $this->uploadFile($request,$fileName,$file_type,$request->input('folder_id'));

            return $this->sendSuccess(['data' => $fileObj]);

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Get media lists
     * 
     * @OA\Get(
     *     path="/api/media/lists",
     *     summary="Get media lists",
     *     description="Get media lists",
     *     tags={"Media"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="file_type",
     *         in="query",
     *         description="File type (image, video, document, etc.)",  
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="s",
     *         in="query",
     *         description="Search keyword",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
          *     @OA\Parameter(
     *         name="folder_id",
     *         in="query",
     *         description="Folder ID to filter files",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=123),
     *                         @OA\Property(property="file_name", type="string", example="image.jpg"),
     *                         @OA\Property(property="file_path", type="string", example="uploads/2024/01/image.jpg"),
     *                         @OA\Property(property="file_size", type="integer", example=1024000),
     *                         @OA\Property(property="file_type", type="string", example="image/jpeg"),
     *                         @OA\Property(property="file_extension", type="string", example="jpg"),
     *                         @OA\Property(property="folder_id", type="integer", example=0),
     *                         @OA\Property(property="author_id", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="totalPage", type="integer", example=4),
     *                 @OA\Property(property="accept", type="string", example="image/*")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - No permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="There is no permission upload"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="uploaded", type="integer", example=0))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="File type not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="File type not found"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="uploaded", type="integer", example=0))
     *         )
     *     )
     * )
     */
    public function getLists(Request $request)
    {
        if (!$this->hasPermissionMedia()) {
            return $this->sendError('There is no permission upload');
        }
        
        $file_type = $request->input('file_type', 'image');
        $s = $request->input('s');
        $model = MediaFile::query();
        if (!Auth::user()->hasPermission("media_manage_others")) {
             $model->where('author_id', Auth::id());
        }
        $uploadConfigs = config('bc.media.groups');

        if(!isset($uploadConfigs[$file_type])){
            return $this->sendError('File type not found');
        }

        $config = isset($uploadConfigs[$file_type]) ? $uploadConfigs[$file_type] : $uploadConfigs['default'];
        $model->whereIn('file_extension',$config['ext']);

        if($folder_id = $request->input('folder_id'))
        {
            $model->where('folder_id',$folder_id);
        }else{
            $model->where('folder_id',0)
            ->orWhere('folder_id',null);
        }
        if ($s) {
            $model->where('file_name', 'like', '%' . ($s) . '%');
        }
        $files = $model->orderBy('id', 'desc')->paginate(32);
        $res = [];
        foreach ($files as $file){
            $res[] = new MediaResource($file);
        }
        return $this->sendSuccess([
            'data'      => $res,
            'total'     => $files->total(),
            'totalPage' => $files->lastPage(),
            'accept' =>$this->getMimeFromType($file_type)
        ]);
    }
}
