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
            'link' => route('admin.tickets.show', $this->ticket->id), // Note: resource route
            'priority' => $this->ticket->priority,
        ];
    }
}

