<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(protected ChatService $chatService)
    {
    }

    /**
     * List all conversations.
     */
    public function chat()
    {
        $conversations = $this->chatService->getConversations(auth()->id());

        return view('dashboard.chat', compact('conversations'));
    }

    /**
     * Show conversation with a specific user.
     */
    public function chatConversation(int $userId)
    {
        $user     = User::findOrFail($userId);
        $messages = $this->chatService->getConversationMessages(auth()->id(), $userId);

        return view('dashboard.chat-conversation', compact('user', 'messages'));
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, int $userId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $this->chatService->sendMessage(auth()->id(), $userId, $request->message);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'          => $message->id,
                    'message'     => $message->message,
                    'sender_id'   => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'created_at'  => $message->created_at->format('H:i'),
                    'sender'      => [
                        'id'   => $message->sender->id,
                        'name' => $message->sender->name,
                    ],
                ],
            ]);
        }

        return redirect()->route('chat.conversation', $userId);
    }

    /**
     * Poll for new messages since a given ID.
     */
    public function getNewMessages(Request $request, int $userId)
    {
        $lastId   = $request->integer('last_message_id', 0);
        $messages = $this->chatService->getNewMessages(auth()->id(), $userId, $lastId);

        return response()->json([
            'success'  => true,
            'messages' => $messages->map(fn($m) => [
                'id'          => $m->id,
                'message'     => $m->message,
                'sender_id'   => $m->sender_id,
                'receiver_id' => $m->receiver_id,
                'created_at'  => $m->created_at->format('H:i'),
                'sender'      => ['id' => $m->sender->id, 'name' => $m->sender->name],
            ]),
        ]);
    }

    /**
     * Broadcast typing indicator.
     */
    public function typing(Request $request, int $userId)
    {
        User::findOrFail($userId); // ensure user exists

        $this->chatService->broadcastTyping(auth()->id(), $userId, auth()->user()->name);

        return response()->json(['success' => true]);
    }
}
