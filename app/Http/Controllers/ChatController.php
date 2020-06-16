<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatMessage;
use App\Comment;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Request $request)
    {
        return view('chat');
    }

    public function comment(Request $request)
    {
        /**
         * ���������. �������� ��������� � ����,
         * ������� ������ Comment $comment � ����������
         */
        $comment = request ('comment');

        broadcast(new ChatMessage($comment))->toOthers(); // ��������� ��������� ����, ����� �������� ������������
    }
}
