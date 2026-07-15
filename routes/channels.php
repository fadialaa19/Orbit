<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// قناة الأدمن
Broadcast::channel('admin.support', function ($user) {
    return in_array($user->role, ['super_admin', 'support_admin']); // تأكد من مسمى الـ role عندك
});

// قناة المجتمعات - أي مستخدم مسجّل دخول (طالب أو أدمن) يقدر ينضم
Broadcast::channel('community.{communityId}', function ($user, $communityId) {
    return true;
});