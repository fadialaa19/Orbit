<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\User;
use App\Models\XpTransaction;
use App\Notifications\NewTicketNotification;
use App\Notifications\StudentAlertNotification;
use Illuminate\Support\Facades\Log;

class XpService
{
    private const MILESTONE = 1000;

    /**
     * Adjust a student's XP (positive to award, negative for an admin
     * deduction) and log an XpTransaction either way. If this crosses a new
     * 1000-point milestone upward, automatically open a support ticket
     * offering a free scholarship application on the student's behalf.
     * Milestones repeat every 1000 XP (2000, 3000, ...), not just the first.
     *
     * $actor is the admin performing a manual adjustment; leave null for
     * system-triggered awards (referral, hourly, etc.).
     */
    public function award(User $user, int $amount, string $reason, ?User $actor = null): void
    {
        if ($amount === 0) {
            return;
        }

        $before = $user->xp;
        $clampedAmount = max($amount, -$before);
        if ($clampedAmount === 0) {
            return;
        }

        $user->increment('xp', $clampedAmount);
        $after = $user->fresh()->xp;

        XpTransaction::create([
            'user_id' => $user->id,
            'amount' => $clampedAmount,
            'reason' => $reason,
            'created_by' => $actor?->id,
        ]);

        Log::info("User ID {$user->id} XP changed by {$clampedAmount} ({$reason}). Total: {$after}");

        if ($clampedAmount > 0) {
            $milestonesBefore = intdiv($before, self::MILESTONE);
            $milestonesAfter = intdiv($after, self::MILESTONE);

            for ($m = $milestonesBefore + 1; $m <= $milestonesAfter; $m++) {
                $this->grantMilestoneReward($user, $m * self::MILESTONE);
            }
        }

        if ($actor) {
            $sign = $clampedAmount > 0 ? '+' : '';
            try {
                $user->notify(new StudentAlertNotification(
                    'تعديل على رصيد نقاطك',
                    "قامت الإدارة بتعديل رصيدك: {$sign}{$clampedAmount} XP ({$reason}). رصيدك الحالي: {$after} XP.",
                    route('dashboard.xp')
                ));
            } catch (\Exception $e) {
                Log::warning('Failed to notify student of manual XP adjustment: ' . $e->getMessage());
            }
        }
    }

    /**
     * Called after anything that could raise a referred student's profile
     * completion (profile save, document upload). Once they cross 50% for
     * the first time, their referrer finally gets the 50 XP for inviting
     * them - a deliberate delay so inviting empty/fake accounts earns
     * nothing until the invitee actually uses the platform.
     */
    public function checkReferralReward(User $referredUser): void
    {
        if (!$referredUser->referred_by || $referredUser->referral_reward_granted) {
            return;
        }

        if ($referredUser->calculateProfileCompletion() < 50) {
            return;
        }

        $referrer = User::find($referredUser->referred_by);

        if ($referrer) {
            $this->award($referrer, 50, "referral (invited user #{$referredUser->id} reached 50% profile completion)");
        }

        $referredUser->update(['referral_reward_granted' => true]);
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
