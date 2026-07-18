<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => '✉️ رسالة تواصل جديدة',
            'body' => $this->contactMessage->name . ': ' . $this->contactMessage->subject,
            'contact_message_id' => $this->contactMessage->id,
            'link' => route('admin.contact-messages.index'),
        ];
    }
}
