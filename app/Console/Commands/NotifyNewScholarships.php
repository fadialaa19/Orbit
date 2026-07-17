<?php

namespace App\Console\Commands;

use App\Models\Scholarship;
use App\Models\User;
use App\Notifications\NewScholarshipPublished;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyNewScholarships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-new-scholarships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email/notify all students about scholarships published since the last run';

    /**
     * Runs on the scheduler (routes/console.php), fully decoupled from the
     * admin's HTTP request. Publishing a scholarship used to notify students
     * synchronously inline, which hung the admin page since the live host
     * runs QUEUE_CONNECTION=sync with no queue worker and each student's
     * email can take several seconds to send.
     */
    public function handle(): void
    {
        $scholarships = Scholarship::where('status', 'active')
            ->whereNull('students_notified_at')
            ->get();

        if ($scholarships->isEmpty()) {
            return;
        }

        $students = User::where('role', 'student')->get();

        foreach ($scholarships as $scholarship) {
            $students->each(function ($student) use ($scholarship) {
                try {
                    $student->notify(new NewScholarshipPublished($scholarship));
                } catch (\Exception $e) {
                    Log::error("Failed to notify student #{$student->id} of new scholarship #{$scholarship->id}: " . $e->getMessage());
                }
            });

            $scholarship->update(['students_notified_at' => now()]);
            $this->info("Notified students about scholarship #{$scholarship->id}: {$scholarship->title_ar}");
        }
    }
}
