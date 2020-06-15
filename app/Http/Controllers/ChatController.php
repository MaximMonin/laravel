<?php

namespace App\Http\Controllers;

use App\Events\ChatMessage;
use App\Comment;

class ChatController extends Controller
{
    public function comment(Request $request)
    {
        /**
         * ¬алидаци€. ƒобавл€ю сообщение в базу,
         * получаю модель Comment $comment с сообщением
         */

        broadcast(new ChatMessage($comment))->toOthers(); // ќтправл€ю сообщение всем, кроме текущего пользовател€
    }
}