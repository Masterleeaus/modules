<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel for the AI assistant sidebar – scoped to the authenticated user.
// The user_identifier stored on threads is "user:{id}".
Broadcast::channel('assistant.user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
