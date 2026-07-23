<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;

class NewTicketNotification extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'تذكرة دعم جديدة #' . $this->ticket->id,
            'body' => $this->ticket->subject . ' (' . $this->ticket->priority . ')',
            'ticket_id' => $this->ticket->id,
            'user_name' => $this->ticket->user->name,
            // admin.tickets.show بيرجع JSON خام (مخصص لطلبات AJAX)، مش صفحة حقيقية -
            // الرابط الصحيح هو صفحة القائمة نفسها مع فتح التذكرة تلقائياً (?open=)
            // وتحديد التبويب الصحيح (دعم/أوراق) حسب بادئة العنوان.
            'link' => route('admin.tickets.index', [
                'view' => str_starts_with($this->ticket->subject ?? '', '📄 طلب استخراج مستند:') ? 'documents' : 'support',
                'open' => $this->ticket->id,
            ]),
            'priority' => $this->ticket->priority,
        ];
    }
}

