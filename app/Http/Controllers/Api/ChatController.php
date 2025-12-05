<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Services\openaiService;
use App\Events\MessageSent;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiTrait;
    public function __construct(public openaiService $openaiService) {}

    /**
     * Get all conversations for the authenticated user.
     */
    public function conversations()
    {
        $messages = Message::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) {
                // Group by the other person in the conversation
                return $message->sender_id === Auth::id() 
                    ? $message->receiver_id 
                    : $message->sender_id;
            });

        return $this->okResponse([
            'conversations' => $messages->map(function ($conversation) {
                return [
                    'user_id' => $conversation->first()->sender_id === Auth::id() 
                        ? $conversation->first()->receiver_id 
                        : $conversation->first()->sender_id,
                    'last_message' => new MessageResource($conversation->first()),
                    'unread_count' => $conversation->where('is_read', false)
                        ->where('receiver_id', Auth::id())
                        ->count(),
                ];
            })->values(),
        ], 'Conversations retrieved successfully');
    }

    /**
     * Get conversation with a specific user.
     */
    public function show($userId)
    {
        $messages = Message::where(function ($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', Auth::id());
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('receiver_id', Auth::id())
            ->where('sender_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return MessageResource::collection($messages);
    }

    /**
     * Send a message.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'ask_ai' => 'sometimes|boolean',
        ]);

        $senderId = Auth::id();
        $receiverId = $validated['receiver_id'];
        $userMessage = $validated['message'];

        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $userMessage,
            'is_ai' => false,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        // Handle AI response if requested
        if ($request->has('ask_ai') && $request->ask_ai == true) {
            $this->openaiService->askAI($userMessage, $senderId, $receiverId);
        }

        $message->load(['sender', 'receiver']);

        return new MessageResource($message);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->receiver_id !== Auth::id()) {
            return $this->forbiddenResponse([], 'Unauthorized');
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return new MessageResource($message);
    }

    /**
     * Get available doctors and admins for chat.
     */
    public function doctors()
    {
        // Return both doctors and admins as support staff
        $supportUsers = User::whereIn('type', ['doctor', 'admin'])->get();

        return $this->okResponse([
            'doctors' => $supportUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'specialization' => $user->specialization ?? 'Support', // Fallback if null
                    'qualification' => $user->qualification ?? null,
                    'image' => $user->image ?? null,
                    'type' => $user->type,
                ];
            }),
        ], 'Doctors retrieved successfully');
    }

    /**
     * Send typing indicator.
     */
    public function typing(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        broadcast(new \App\Events\UserTyping(
            Auth::id(),
            $validated['receiver_id'],
            Auth::user()->name
        ))->toOthers();

        return $this->okResponse([], 'Typing indicator sent');
    }
}
