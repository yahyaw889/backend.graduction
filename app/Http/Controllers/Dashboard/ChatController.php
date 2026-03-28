<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function chat()
    {
        // Get latest message ID for each sender-receiver pair
        $latestMessageIds = Message::selectRaw('MAX(id) as id')
            ->groupBy('sender_id', 'receiver_id')
            ->pluck('id');

        $conversations = Message::whereIn('id', $latestMessageIds)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) {
                return min($message->sender_id, $message->receiver_id).'-'.max($message->sender_id, $message->receiver_id);
            })
            ->map(function ($group) {
                return $group->first();
            });

        return view('dashboard.chat', compact('conversations'));
    }

    public function chatConversation($userId)
    {
        $user = User::findOrFail($userId);

        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', auth()->guard('web')->user()->id)
                ->where('receiver_id', $userId);
        })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', auth()->guard('web')->user()->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('receiver_id', auth()->guard('web')->user()->id)
            ->where('sender_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('dashboard.chat-conversation', compact('user', 'messages'));
    }

    public function sendMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->guard('web')->user()->id,
            'receiver_id' => $userId,
            'message' => $request->message,
            'is_ai' => false,
        ]);

        // Load relationships for broadcasting
        $message->load(['sender', 'receiver']);

        // Broadcast the message in real-time
        broadcast(new MessageSent($message))->toOthers();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'created_at' => $message->created_at->format('H:i'),
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                    ],
                ],
            ]);
        }

        return redirect()->route('dashboard.chat.conversation', $userId);
    }

    public function getNewMessages(Request $request, $userId)
    {
        $lastMessageId = $request->query('last_message_id', 0);

        $messages = Message::where('id', '>', $lastMessageId)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', auth()->guard('web')->user()->id)
                    ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($userId, $lastMessageId) {
                $query->where('id', '>', $lastMessageId)
                    ->where('sender_id', $userId)
                    ->where('receiver_id', auth()->guard('web')->user()->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'created_at' => $message->created_at->format('H:i'),
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                    ],
                ];
            }),
        ]);
    }

    public function typing(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        broadcast(new UserTyping(
            auth()->guard('web')->user()->id,
            $userId,
            auth()->guard('web')->user()->name
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
