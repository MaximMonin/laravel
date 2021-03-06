<?php

namespace App\Http\Controllers;

use App\Message;
use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chat');
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages()
    {
       return Message::latest()->with(array('user'=>function($query){
             $query->select('id','name','avatar');}))->paginate(50)->getCollection();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message'),
            'files' => $request->input('files'),
        ]);

        broadcast(new ChatMessage($user, $message))->toOthers();

        return ['status' => 'Message Sent!'];
    }
}
