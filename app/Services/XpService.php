<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewTicketNotification;
use App\Notifications\StudentAlertNotification;
use Illuminate\Support\Facades\Log;

class XpService
{
    private const MILESTONE = 1000;

    /**
     * Award XP to a student and, if this crosses a new 1000-point milestone,
     * automatically open a support ticket offering a free scholarship
     * application on the student's behalf. Milestones repeat every 1000 XP
     * (2000, 3000, ...), not just the first time.
     */
    public function award(User $user, int $amount, string $reason): void
    {
        if ($amount <= 0) {
            return;
        }

        $before = $user->xp;
        $user->increment('xp', $amount);
        $after = $user->fresh()->xp;

        Log::info("User ID {$user->id} earned {$amount} XP ({$reason}). Total: {$after}");

        $milestonesBefore = intdiv($before, self::MILESTONE);
        $milestonesAfter = intdiv($after, self::MILESTONE);

        for ($m = $milestonesBefore + 1; $m <= $milestonesAfter; $m++) {
            $this->grantMilestoneReward($user, $m * self::MILESTONE);
        }
    }

    private function grantMilestoneReward(User $user, int $milestoneXp): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => "🎁 مكافأة {$milestoneXp} XP: تقديم مجاني على منحة",
            'priority' => 'high',
            'status' => 'pending',
            'ai_summary' => "وصل الطالب {$user->name} إلى {$milestoneXp} نقطة XP، ما يستحقه تقديم مجاني على منحة من طرف فريق أوربيت. يرجى التواصل معه لمعرفة المنحة المطلوبة ومتابعة التقديم نيابة عنه.",
        ]);

        try {
            User::admins()->get()->each->notify(new NewTicketNotification($ticket));
        } catch (\Exception $e) {
            Log::warning('Failed to notify admins of XP milestone ticket: ' . $e->getMessage());
        }

        try {
            $user->notify(new StudentAlertNotification(
                '🎉 وصلت إلى ' . $milestoneXp . ' نقطة!',
                'مبروك! وصلت إلى ' . $milestoneXp . ' نقطة XP وتستحق تقديم مجاني على منحة من طرفنا. فريقنا سيتواصل معك قريباً لمعرفة المنحة اللي بدك نساعدك فيها.',
                route('dashboard.communications')
            ));
        } catch (\Exception $e) {
            Log::warning('Failed to notify student of XP milestone: ' . $e->getMessage());
        }
    }
}
