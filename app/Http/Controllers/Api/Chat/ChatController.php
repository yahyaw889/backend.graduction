<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Message;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Services\openaiService;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    use ApiTrait;

    public function __construct(public openaiService $openaiService){}
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;
        $userMessage = $request->message;

        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $userMessage,
            'is_ai' => false,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        if ($request->has('ask_ai') && $request->ask_ai == true) {
            $this->openaiService->askAI($userMessage, $senderId, $receiverId);
        }
        return $this->okResponse( $message, 'Message sent successfully');
    }

    public function getMessages($userId)
    {
        $currentUserId = Auth::id();

        $messages = Message::where(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $currentUserId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $currentUserId);
        })->with('sender')->orderBy('created_at', 'asc')->get();

        return $this->okResponse((array) $messages, 'Messages retrieved successfully');

    }

}
