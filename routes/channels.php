<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels - Real-time Communication
|--------------------------------------------------------------------------
|
| WebSocket/Pusher channel authorization for real-time features.
| Channels define who can listen to specific broadcast events.
| 
| Used for: admin notifications, live updates, user-specific events
| 
| @author SlowWebDev
|
*/

/*
|--------------------------------------------------------------------------
| User-specific Private Channels
|--------------------------------------------------------------------------
|
| Private channels for individual user notifications and updates
|
*/

// Private channel for user-specific events (notifications, security alerts, etc.)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Admin-only Channels
|--------------------------------------------------------------------------
|
| Future admin-specific channels for dashboard updates, system alerts
| Example: admin activity logs, security events, system status
|
*/
