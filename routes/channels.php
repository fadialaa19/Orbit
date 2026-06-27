<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// قناة الأدمن
Broadcast::channel('admin.support', function ($user) {
    return in_array($user->role, ['super_admin', 'support_admin']); // تأكد من مسمى الـ role عندك
});