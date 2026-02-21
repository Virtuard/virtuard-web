<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\NotificationPush;

class VendorNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Notification List & Filter
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, unread, read
        $search = $request->get('search'); // search keyword
        $userId = Auth::id();

        $query = NotificationPush::query()
            ->where('notifiable_id', $userId)
            ->where('for_admin', 0); // Vendor notifications are not for admin only

        if ($type === 'unread') {
            $query->whereNull('read_at');
        } elseif ($type === 'read') {
            $query->whereNotNull('read_at');
        }

        if (!empty($search)) {
            $query->where('data', 'LIKE', '%' . $search . '%');
        }

        $query->orderBy('created_at', 'desc');

        $notifications = $query->paginate(20)->appends($request->query());

        // Format data for frontend
        $notifications->getCollection()->transform(function ($notif) {
            $data = is_string($notif->data) ? json_decode($notif->data, true) : $notif->data;

            $avatar = $data['avatar'] ?? null;
            $name = $data['name'] ?? '';
            $title = $data['title'] ?? '';
            $link = $data['link'] ?? '#';
            $type = $data['type'] ?? 'system';

            return [
                'id' => $notif->id,
                'avatar' => $avatar,
                'name' => $name,
                'title' => $title,
                'link' => $link,
                'type' => $type,
                'is_read' => !empty($notif->read_at),
                'created_at' => $notif->created_at,
                'time_ago' => format_interval($notif->created_at),
            ];
        });

        $data = [
            'page_title' => __('Notifications'),
            'current_type' => $type,
            'notifications' => $notifications,
        ];

        return view('v2.vendor.notification.index', $data);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request)
    {
        $id = $request->input('id');

        if (!empty($id)) {
            NotificationPush::query()
                ->where('id', $id)
                ->where('notifiable_id', Auth::id())
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'status' => true,
            'message' => __('Notification marked as read')
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        NotificationPush::query()
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'status' => true,
            'message' => __('All notifications marked as read')
        ]);
    }
}
