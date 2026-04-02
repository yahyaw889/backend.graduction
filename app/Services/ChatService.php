<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatService
{
    /**
     * Get all unique conversations for a user, with latest message per conversation.
     */
    public function getConversations(int $userId): Collection
    {
        // Get latest message ID for each unique conversation pair
        $latestMessageIds = Message::selectRaw('MAX(id) as id')
            ->groupBy('sender_id', 'receiver_id')
            ->pluck('id');

        return Message::whereIn('id', $latestMessageIds)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) {
                return min($message->sender_id, $message->receiver_id)
                    . '-' . max($message->sender_id, $message->receiver_id);
            })
            ->map(fn($group) => $group->first());
    }

    /**
     * Get conversation messages between two users, mark as read.
     */
    public function getConversationMessages(int $authId, int $otherUserId): Collection
    {
        $messages = Message::where(function ($q) use ($authId, $otherUserId) {
                $q->where('sender_id', $authId)->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($authId, $otherUserId) {
                $q->where('sender_id', $otherUserId)->where('receiver_id', $authId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('receiver_id', $authId)
            ->where('sender_id', $otherUserId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $messages;
    }

    /**
     * Send a message from sender to receiver.
     */
    public function sendMessage(int $senderId, int $receiverId, string $content): Message
    {
        $message = Message::create([
            'sender_id'   => $senderId,
            'receiver_id' => $receiverId,
            'message'     => $content,
            'is_ai'       => false,
        ]);

        $message->load(['sender', 'receiver']);

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Mark a single message as read.
     */
    public function markAsRead(Message $message): Message
    {
        $message->update(['is_read' => true, 'read_at' => now()]);
        return $message;
    }

    /**
     * Get new messages since a given ID in a conversation.
     */
    public function getNewMessages(int $authId, int $otherUserId, int $lastMessageId): Collection
    {
        return Message::where('id', '>', $lastMessageId)
            ->where(function ($q) use ($authId, $otherUserId) {
                $q->where(fn($q) => $q->where('sender_id', $authId)->where('receiver_id', $otherUserId))
                  ->orWhere(fn($q) => $q->where('sender_id', $otherUserId)->where('receiver_id', $authId));
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Broadcast typing indicator.
     */
    public function broadcastTyping(int $senderId, int $receiverId, string $senderName): void
    {
        broadcast(new UserTyping($senderId, $receiverId, $senderName))->toOthers();
    }

    /**
     * Get all support users (doctors + admins).
     */
    public function getSupportUsers(): Collection
    {
        return User::whereIn('type', ['doctor', 'admin'])->get();
    }
}
