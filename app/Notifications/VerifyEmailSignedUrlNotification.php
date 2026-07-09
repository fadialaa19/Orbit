<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailSignedUrlNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    // 1. توليد الرابط النصي المشفر بدون دالة toHtml()
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]
    );

    // 2. توجيه الرابط واسم المستخدم إلى قالب الـ Blade المخصص
    return (new \Illuminate\Notifications\Messages\MailMessage)
        ->view('emails.verify-email', [ // تأكد من مطابقة اسم مسار ملف الـ Blade لديك
            'verificationUrl' => $verificationUrl,
            'name' => $notifiable->name // تمرير اسم المستخدم ليظهر في الإيميل
        ]);
}
}

