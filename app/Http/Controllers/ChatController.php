<?php

namespace App\Http\Controllers;

use App\Events\ChatMessage;
use App\Comment;

class ChatController extends Controller
{
    public function comment(Request $request)
    {
        /**
         * ���������. �������� ��������� � ����,
         * ������� ������ Comment $comment � ����������
         */

        broadcast(new ChatMessage($comment))->toOthers(); // ��������� ��������� ����, ����� �������� ������������
    }
}