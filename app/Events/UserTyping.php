<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;
    public $senderName;

    public function __construct($senderId, $receiverId, $senderName)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->senderName = $senderName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->getChannelId());
    }

    private function getChannelId()
    {
        $ids = [$this->senderId, $this->receiverId];
        sort($ids);
        return implode('-', $ids);
    }

    public function broadcastWith()
    {
        return [
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
        ];
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }
}
