<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Services\ChatService;
use App\Services\openaiService;
use App\Traits\ApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiTrait;

    public function __construct(
        protected ChatService $chatService,
        protected openaiService $openaiService
    ) {
    }

    /**
     * Get all conversations for the authenticated user.
     */
    public function conversations(): JsonResponse
    {
        $userId    = Auth::id();
        $messages  = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id === $userId
                    ? $message->receiver_id
                    : $message->sender_id;
            });

        $conversations = $messages->map(function ($group) use ($userId) {
            $latest = $group->first();
            return [
                'user_id'      => $latest->sender_id === $userId ? $latest->receiver_id : $latest->sender_id,
                'last_message' => new MessageResource($latest),
                'unread_count' => $group->where('is_read', false)->where('receiver_id', $userId)->count(),
            ];
        })->values();

        return $this->okResponse(
            ['conversations' => $conversations],
            'Conversations retrieved successfully'
        );
    }

    /**
     * Get messages in a specific conversation.
     */
    public function show(int $userId): JsonResponse
    {
        $messages = $this->chatService->getConversationMessages(Auth::id(), $userId);

        return $this->okResponse(
            MessageResource::collection($messages),
            'Conversation retrieved successfully'
        );
    }

    /**
     * Send a message (optionally trigger AI response).
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:2000',
            'ask_ai'      => 'sometimes|boolean',
        ]);

        $message = $this->chatService->sendMessage(Auth::id(), $validated['receiver_id'], $validated['message']);

        if ($request->boolean('ask_ai')) {
            $this->openaiService->askAI($validated['message'], Auth::id(), $validated['receiver_id']);
        }

        return $this->createdResponse(
            new MessageResource($message),
            'Message sent successfully'
        );
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(int $messageId): JsonResponse
    {
        $message = Message::findOrFail($messageId);

        if ($message->receiver_id !== Auth::id()) {
            return $this->forbiddenResponse([], 'You cannot mark this message as read');
        }

        $message = $this->chatService->markAsRead($message);

        return $this->okResponse(
            new MessageResource($message),
            'Message marked as read'
        );
    }

    /**
     * Get available doctors and admins for chat.
     */
    public function doctors(): JsonResponse
    {
        $supportUsers = $this->chatService->getSupportUsers()->map(fn($user) => [
            'id'             => $user->id,
            'name'           => $user->name,
            'specialization' => $user->specialization ?? 'Support',
            'qualification'  => $user->qualification  ?? null,
            'image'          => $user->image          ?? null,
            'type'           => $user->type,
        ]);

        return $this->okResponse(
            ['doctors' => $supportUsers],
            'Support users retrieved successfully'
        );
    }

    /**
     * Send typing indicator.
     */
    public function typing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $this->chatService->broadcastTyping(Auth::id(), $validated['receiver_id'], Auth::user()->name);

        return $this->okResponse([], 'Typing indicator sent');
    }
}
