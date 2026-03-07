<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Story;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class StoryController extends Controller
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
    public function addStory(Request $request)
    {
        try{
        $this->validate($request, [
            'media' => 'required|mimes:jpeg,png,mp4|max:20000',
        ]);

        $idUser = Auth::id();

        // Periksa apakah file media telah diunggah
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('/story');
        } else {
            $mediaPath = null;
        }

        $story = new Story;
        $story->user_id = $idUser;
        $story->link_text = $request->input('linkText');
        $story->link = $request->input('link');
        $story->media = $mediaPath;

        $story->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Add story successfully',
            'data' => $request->all(),
        ]);
        }catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Someting wrong. Add story error!',
            ]);
        }
    }

    public function getStory()
    {
        $idUser = Auth::id();

        $story = Story::where('user_id', $idUser)->get();
        ;

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diproses',
            'data' => $story,
        ]);
    }

    public function destroy($id)
    {
        try {
            Story::destroy($id);

            return response()
                    ->json([
                        'status' => true,
                    ]);
        } catch (Exception $e) {
            return response()
                    ->json([
                        'status' => false,
                    ]);
        }
    }

}
