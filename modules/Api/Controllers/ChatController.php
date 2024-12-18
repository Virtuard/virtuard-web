<?php

namespace Modules\Api\Controllers;

use App\User;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\ChMessage as Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;


class ChatController  extends \Chatify\Http\Controllers\MessagesController
{
    protected $messengerFallbackColor = '#2180f3';

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function iframe($id = null)
    {
        if (!setting_item('inbox_enable')) abort(404);

        $routeName = FacadesRequest::route()->getName();
        $type = in_array($routeName, ['user', 'group'])
            ? $routeName
            : 'user';

        return view('Chatify::pages.app', [
            'id' => $id ?? 0,
            'type' => $type ?? 'user',
            'messengerColor' => Auth::user()->messenger_color ?? $this->messengerFallbackColor,
            'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark', 
        ]);
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $getRecords = null;
        $input = trim(filter_var($request['input'], FILTER_SANITIZE_STRING));
        $records = \App\Models\User::where('id', '!=', Auth::user()->id)
            ->where('name', 'LIKE', "%{$input}%")
            ->orWhere('first_name', 'LIKE', "%{$input}%")
            ->orWhere('email', 'LIKE', "%{$input}%")
            ->paginate($request->per_page ?? $this->perPage);
        foreach ($records->items() as $record) {
            $getRecords .= view('Chatify::layouts.listItem', [
                'get' => 'search_item',
                'type' => 'user',
                'user' => $record,
            ])->render();
        }
        if ($records->total() < 1) {
            $getRecords = '<p class="message-hint center-el"><span>Nothing to show.</span></p>';
        }
        // send the response
        return Response::json([
            'records' => $getRecords,
            'total' => $records->total(),
            'last_page' => $records->lastPage()
        ], 200);
    }

    /**
     * Get contacts list
     *
     * @param Request $request
     * @return JSON response
     */
    public function getContacts(Request $request)
    {
        $user_id = intval($request->query('user_id'));
        $tmpUser = $user_id ? User::find($user_id) : false;

        $users = Message::join('users', function ($join) {
            $join->on('ch_messages.from_id', '=', 'users.id')
                ->orOn('ch_messages.to_id', '=', 'users.id');
        })
            ->where(function ($q) {
                $q->where('ch_messages.from_id', Auth::user()->id)
                    ->orWhere('ch_messages.to_id', Auth::user()->id);
            })
            ->select('users.*', DB::raw('MAX(ch_messages.created_at) as max_created_at'))
            ->orderBy('max_created_at', 'desc')
            ->groupBy('users.id')
            ->paginate($request->per_page ?? $this->perPage);

        $usersList = $users->items();

        $contacts = [];

        if (count($usersList) > 0) {
            foreach ($usersList as $user) {
                if ($user->id == Auth::user()->id) {
                    continue;
                }

                $lastMessage = Message::where('from_id', Auth::user()->id)
                    ->where('to_id', $user->id)
                    ->latest('created_at')
                    ->first();

                if (!$lastMessage) {
                    $lastMessage = Message::where('from_id', $user->id)
                        ->where('to_id', Auth::user()->id)
                        ->latest('created_at')
                        ->first();
                }

                $contacts[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => url('storage/users-avatar/' . $user->avatar_url), 
                    'last_message' => $lastMessage ? $lastMessage->body : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'has_attachment' => $lastMessage && $lastMessage->has_attachment ? true : false,
                ];
            }
        } else {
            $contacts = [
                'message' => 'Your contact list is empty',
            ];
        }

        if ($tmpUser && ($usersList || !in_array($user_id, $users->pluck('id')->all()))) {
            if ($tmpUser->id == Auth::user()->id) {
                return response()->json([
                    'contacts' => [],
                    'total' => 0,
                    'last_page' => 1,
                ], 200);
            }

            $contacts[] = [
                'id' => $tmpUser->id,
                'name' => $tmpUser->name,
                'avatar_url' => url('storage/users-avatar/' . $tmpUser->avatar_url),
                'last_message' => null,
                'last_message_time' => null,
                'has_attachment' => false,
            ];
        }

        return Response::json([
            'contacts' => $contacts,
            'total' => $users->total(),
            'last_page' => $users->lastPage(),
        ], 200);
    }




    /**
     * Fetch data by id for (user/group)
     *
     * @param Request $request
     * @return collection
     */
    public function idFetchData(Request $request)
    {
        $favorite = Chatify::inFavorite($request['id']);

        if ($request['type'] == 'user') {
            $fetch = \App\Models\User::where('id', $request['id'])->first();
            if ($fetch) {
                $userAvatar = $fetch->avatar_url;
            }
        }

        return Response::json([
            'favorite' => $favorite,
            'fetch' => $fetch ?? [],
            'user_avatar' => $userAvatar ?? null,
        ]);
    }


    public function fetch(Request $request)
    {
        $toId = $request['to_id'];
    
        $query = Chatify::fetchMessagesQuery($toId)
            ->where(function($q) {
                $q->where('from_id', Auth::user()->id)
                  ->orWhere('to_id', Auth::user()->id);
            })
            ->where(function($q) use ($toId) {
                $q->where('from_id', $toId)
                  ->orWhere('to_id', $toId);
            })
            ->latest('created_at'); 
    
        $messages = $query->paginate($request->per_page ?? $this->perPage);
    
        $totalMessages = $messages->total();
        $lastPage = $messages->lastPage();
    
        $response = [
            'total' => $totalMessages,
            'last_page' => $lastPage,
            'last_message_id' => collect($messages->items())->last()->id ?? null,
            'messages' => [],
        ];
    
        if ($totalMessages < 1) {
            $response['messages'] = []; 
            return Response::json($response);
        }
    
        foreach ($messages->reverse() as $message) {
            $response['messages'][] = [
                'id' => $message->id,
                'from_id' => $message->from_id,
                'to_id' => $message->to_id,
                'body' => $message->body,
                'created_at' => $message->created_at->diffForHumans(), 
                'updated_at' => $message->updated_at->diffForHumans(),
                'attachments' => $message->attachments, 
                'sender' => $message->from_id == Auth::user()->id, 
            ];
        }
    
        return Response::json($response);
    }
    
    public function send(Request $request)
{
    $error = (object)[
        'status' => 0,
        'message' => null
    ];
    $attachment = null;
    $attachment_title = null;

    if ($request->hasFile('file')) {
        $allowed_images = Chatify::getAllowedImages();
        $allowed_files  = Chatify::getAllowedFiles();
        $allowed        = array_merge($allowed_images, $allowed_files);

        $file = $request->file('file');
        if ($file->getSize() < Chatify::getMaxUploadSize()) {
            if (in_array(strtolower($file->extension()), $allowed)) {
                $attachment_title = $file->getClientOriginalName();
                $attachment = Str::uuid() . "." . $file->extension();
                $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
            } else {
                $error->status = 1;
                $error->message = "Ekstensi file tidak diizinkan!";
            }
        } else {
            $error->status = 1;
            $error->message = "Ukuran file terlalu besar!";
        }
    }

    if (!$error->status) {
        $message = Chatify::newMessage([
            'from_id' => Auth::user()->id,
            'to_id' => $request['id'],
            'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
            'attachment' => ($attachment) ? json_encode((object)[
                'new_name' => $attachment,
                'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
            ]) : null,
        ]);
        
        $messageData = Chatify::parseMessage($message);
        
        if (Auth::user()->id != $request['id']) {
            Chatify::push("private-chatify.".$request['id'], 'messaging', [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'message' => Chatify::messageCard($messageData, true)
            ]);
        }
    }

    return Response::json([
        'status' => '200',
        'error' => $error,
        'message' => Chatify::messageCard(@$messageData),
        'tempID' => $request['temporaryMsgId'],
    ]);
}



}
