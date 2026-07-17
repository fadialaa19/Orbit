<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiter for Groq AI Chat API
        // Identifies by authenticated user ID when available, otherwise by IP address
        RateLimiter::for('groq-chat', function ($request) {
            $key = $request->user()?->id ?? $request->ip();
            return Limit::perMinute(10)->by($key);
        });

        // Rate limiter for AI scholarship match-score computation
        RateLimiter::for('match-scores', function ($request) {
            $key = $request->user()?->id ?? $request->ip();
            return Limit::perMinute(6)->by($key);
        });

        // Rate limiter for the XP time-on-site heartbeat (client pings every ~5 minutes)
        RateLimiter::for('xp-heartbeat', function ($request) {
            $key = $request->user()?->id ?? $request->ip();
            return Limit::perMinute(3)->by($key);
        });

        // Outgoing mail sends from a domain address (for SPF/DKIM/deliverability),
        // but replies should still land in a real inbox - see config/mail.php.
        if ($replyToAddress = config('mail.reply_to.address')) {
            Mail::alwaysReplyTo($replyToAddress, config('mail.reply_to.name'));
        }
    }
}

