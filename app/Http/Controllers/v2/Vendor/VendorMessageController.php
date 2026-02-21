<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor Messages Page (V2)
     */
    public function index(Request $request)
    {
        // Get pre-selected user ID if chat is initiated from somewhere else
        $userId = $request->input('user_id');

        $data = [
            'page_title' => __('Messages'),
            'user_id' => $userId, // the ID of the person we are chatting with

            // Helpful variables for the frontend to hook into Chatify APIs
            'messenger_color' => config('chatify.colors.primary', '#2180f3'),
            'dark_mode' => config('chatify.dark_mode', 'light'),
            'chatify_api' => [
                'fetch_contacts' => url('/chatify/api/fetchContacts'),
                'fetch_messages' => url('/chatify/api/fetchMessages'),
                'send_message' => url('/chatify/api/sendMessage'),
            ],
            // Alternative: Simply use the iframe route like V1 uses
            'iframe_url' => route(config('chatify.path', 'chatify'), ['user_id' => $userId])
        ];

        return view('v2.vendor.messages.index', $data);
    }
}
