<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/**
 * Private chat channel: chat.{userId}
 * A user can only subscribe to their OWN private channel.
 */
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Presence channel for live "who is online" in a conversation.
 * Optional — use if you want to show online presence indicators.
 */
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // TODO: Check if the user is a participant in this conversation
    // $conversation = \App\Models\Conversation::find($conversationId);
    // if ($conversation && $conversation->hasParticipant($user->id)) {
    //     return ['id' => $user->id, 'name' => $user->name];
    // }
    // return false;

    return ['id' => $user->id, 'name' => $user->name]; // Allow all for now
});
