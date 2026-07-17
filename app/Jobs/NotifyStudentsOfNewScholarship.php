<?php

namespace App\Jobs;

use App\Models\Scholarship;
use App\Models\User;
use App\Notifications\NewScholarshipPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifyStudentsOfNewScholarship implements ShouldQueue
{
    use Queueable;

    public function __construct(public Scholarship $scholarship)
    {
    }

    /**
     * Dispatched with ->afterResponse() so the admin's page never waits on
     * this - on hosts running QUEUE_CONNECTION=sync (no worker), a ShouldQueue
     * job still executes inline otherwise, and mailing every student (each
     * SMTP send can take several seconds) would make scholarship creation
     * hang until every email finished sending.
     */
    public function handle(): void
    {
        User::where('role', 'student')->get()->each(function ($student) {
            try {
                $student->notify(new NewScholarshipPublished($this->scholarship));
            } catch (\Exception $e) {
                Log::error("Failed to notify student #{$student->id} of new scholarship #{$this->scholarship->id}: " . $e->getMessage());
            }
        });
    }
}
