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

        // Outgoing mail sends from a domain address (for SPF/DKIM/deliverability),
        // but replies should still land in a real inbox - see config/mail.php.
        if ($replyToAddress = config('mail.reply_to.address')) {
            Mail::alwaysReplyTo($replyToAddress, config('mail.reply_to.name'));
        }
    }
}

