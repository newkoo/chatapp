<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function rooms(Request $request)
    {
        return ChatRoom::all();
    }
    public function messages(Request $request, $roomid)
    {
        return
            ChatMessage::where('room_id', $roomid)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }
    public function newMessage(Request $request,$roomid)
    {
        $newMessage=new ChatMessage;
        $newMessage->user_id=Auth::id();
        $newMessage->room_id=$roomid;
        $newMessage->message=$request->message;
        $newMessage->save();

        return $newMessage;
    }
}
