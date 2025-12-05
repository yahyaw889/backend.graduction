<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channel authorization
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // chatId format: "userId1-userId2" (sorted)
    $userIds = explode('-', $chatId);
    
    // User can access the channel if they are one of the participants
    return in_array($user->id, $userIds);
});
