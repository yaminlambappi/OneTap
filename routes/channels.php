<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('campus.{campusId}', function ($user, $campusId) {
    if ($campusId === 'global' || (int) $user->campus_id === (int) $campusId) {
        return ['id' => $user->id, 'name' => $user->display_name, 'avatar' => $user->avatar_url];
    }
    return false;
});

Broadcast::channel('live-room.{roomId}', function ($user, $roomId) {
    // You can add logic to restrict access if private
    return [
        'id' => $user->id,
        'name' => $user->display_name,
        'avatar' => $user->avatar_url
    ];
});
