<?php

namespace App\Notifications;

use App\Models\Scholarship;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewScholarshipPublished extends Notification
{
    // بدون ShouldQueue عمداً: بتترسل من جوا app:notify-new-scholarships
    // (مجدولة كل دقيقة، مش من طلب الأدمن مباشرة)، فمافي داعي نأجّل التنفيذ
    // لصف انتظار - وربطها بـ ShouldQueue كان بيحتاج queue worker شغال
    // عشان تترسل فعلياً، وهو مش متوفر على الاستضافة الحية.

    public function __construct(public Scholarship $scholarship)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => '🎓 منحة جديدة: ' . $this->scholarship->title_ar,
            'body' => $this->scholarship->university . ' - ' . $this->scholarship->country,
            'scholarship_id' => $this->scholarship->id,
            'link' => route('dashboard.scholarships.show', $this->scholarship->id),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎓 منحة جديدة على Orbit: ' . $this->scholarship->title_ar)
            ->view('emails.new-scholarship', [
                'studentName' => $notifiable->name,
                'scholarship' => $this->scholarship,
                'scholarshipUrl' => route('dashboard.scholarships.show', $this->scholarship->id),
            ]);
    }
}
