<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Ipanorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorVirtuardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor Virtuard 360 Index (V2)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $search = $request->input('search');
        $statusFilter = $request->input('status', 'all'); // 'all', 'publish', 'draft'
        $sortFilter = $request->input('sort', 'newest'); // 'newest', 'oldest', 'title_az', 'title_za', 'most_used'

        $query = Ipanorama::query()
            ->where('user_id', $userId)
            ->withCount(['hotels', 'spaces', 'businesses']); // Used for calculating "most used"

        // Filter: Search by Title
        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Filter: Status
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Sort
        switch ($sortFilter) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_az':
                $query->orderBy('title', 'asc');
                break;
            case 'title_za':
                $query->orderBy('title', 'desc');
                break;
            case 'most_used':
                // Sum the counts. We can order by raw sum of these counts in SQL.
                $query->orderByRaw('(hotels_count + spaces_count + businesses_count) DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $paginator = $query->paginate(10)->appends($request->query());

        // Transform collection for Data Table view
        $paginator->through(function ($pan) {
            return [
                'id' => $pan->id,
                'uuid' => $pan->uuid,
                'title' => $pan->title,
                'status' => $pan->status,
                'usage_count' => $pan->hotels_count + $pan->spaces_count + $pan->businesses_count, // Total times used
                'detail_url' => route('user.virtuard-360.show', $pan->id), // Can reuse V1 preview routes if needed or define new V2 ones
                'edit_url' => route('vendor2.virtuard360.edit', ['id' => $pan->id]),
                'delete_url' => route('vendor2.virtuard360.delete', $pan->id),
                'share_url' => route('panorama.share', ['id' => $pan->uuid]),
                'publish_url' => route('vendor2.virtuard360.updateStatus', ['id' => $pan->id, 'action' => 'make-publish']),
                'draft_url' => route('vendor2.virtuard360.updateStatus', ['id' => $pan->id, 'action' => 'make-hide']),
            ];
        });

        $data = [
            'page_title' => __('Virtuard Asset 360°'),
            'panoramas' => $paginator,
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'sort' => $sortFilter,
            ]
        ];

        return view('v2.vendor.virtuard360.index', $data);
    }

    /**
     * Change Status via GET/POST
     */
    public function updateStatus(Request $request, $id)
    {
        $action = $request->input('action');
        $userId = Auth::id();

        $panorama = Ipanorama::where("user_id", $userId)->where("id", $id)->first();

        if (empty($panorama)) {
            return redirect()->back()->with('error', __('Asset Not Found'));
        }

        if ($action === 'make-hide') {
            $panorama->status = "draft";
        } elseif ($action === 'make-publish') {
            // Include plan check if required structurally
            if (!auth()->user()->checkUserIpanoramaPlan()) {
                return redirect(route('user.plan'))->with('error', __('Upgrade plan required'));
            }
            $panorama->status = "publish";
        }

        $panorama->save();
        return redirect()->back()->with('success', __('Status updated successfully!'));
    }

    /**
     * Delete existing Asset
     */
    public function delete($id)
    {
        $userId = Auth::id();
        $panorama = Ipanorama::where("user_id", $userId)->where("id", $id)->first();

        if ($panorama) {
            $panorama->delete();
            return redirect()->back()->with('success', __('Asset deleted successfully'));
        }

        return redirect()->back()->with('error', __('Asset not found or no permission'));
    }

    /**
     * Show preview Frame
     */
    public function show($id)
    {
        $userId = Auth::id();
        $panorama = Ipanorama::where("user_id", $userId)->where("id", $id)->firstOrFail();

        $data = [
            'page_title' => __('Detail Virtuard Asset 360°'),
            'panorama' => $panorama,
            'share_url' => route('panorama.share', ['id' => $panorama->uuid])
        ];

        return view('v2.vendor.virtuard360.show', $data);
    }

    /**
     * Add New Asset 360 Wizard
     */
    public function add(Request $request)
    {
        $data = [
            'page_title' => __('Add New Virtuard Asset 360°'),
            'panorama' => null, // Tells blade this is creation phase
        ];

        return view('v2.vendor.virtuard360.add', $data);
    }

    /**
     * Edit existing Asset 360 Wizard
     */
    public function edit(Request $request)
    {
        $userId = Auth::id();
        $id = $request->query('id');

        $panorama = null;
        if ($id) {
            $panorama = Ipanorama::where("user_id", $userId)->where("id", $id)->firstOrFail();
        }

        $data = [
            'page_title' => __('Edit Virtuard Asset 360°'),
            'panorama' => $panorama,
            'user_id' => $userId,
            'page' => $request->query('page', 'edit'),
            'wstep' => $request->query('wstep', 1)
        ];

        return view('v2.vendor.virtuard360.edit', $data);
    }

    /**
     * Store new Title
     */
    public function store(Request $request)
    {
        if (!auth()->user()->checkUserIpanoramaPlan()) {
            return redirect(route('my-plans.index'))->with('error', 'Please upgrade your plan first');
        }

        $idUser = Auth::id();

        $ipanorama = new Ipanorama();
        $ipanorama->user_id = $idUser;
        $ipanorama->create_user = $idUser;
        $ipanorama->title = $request->input('title');
        $ipanorama->status = 'draft';
        $ipanorama->save();

        $urlWithId = route('vendor2.virtuard360.edit', [
            'id' => $ipanorama->id,
            'user_id' => $idUser,
            'page' => $request->input('page'),
            'wstep' => $request->input('wstep'),
        ]);
        return redirect($urlWithId)->with('success', 'Insert successfully');
    }

    /**
     * Store New Image
     */
    public function storeImage(Request $request)
    {
        $this->validate($request, [
            'images.*' => 'required|mimes:jpeg,png,webp',
        ]);

        $proofImage = $request->file('images');

        foreach ($proofImage as $image) {
            $path = "/ipanoramaBuilder/upload/" . $request['user_id'] . "/" . $request->panorama_id;
            if ($image) {
                $newFileName = now()->format('ymd') . '-' . $request->panorama_id . '-' . $request->user_id . '-' . $image->getClientOriginalName();
                $image->storeAs($path, $newFileName);
            }
        }

        return redirect()->route('vendor2.virtuard360.edit', [
            'id' => $request->panorama_id,
            'user_id' => $request->user_id,
            'page' => $request->page ? $request->page : '',
            'wstep' => $request->wstep ? $request->wstep + 1 : ''
        ])->with('success', 'Image Insert successfully');
    }
}
