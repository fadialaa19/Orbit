<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust the reverse proxy Render (and most PaaS hosts) put in front of
        // the app, so Laravel correctly detects HTTPS via X-Forwarded-Proto
        // instead of generating http:// asset/URL links behind an https:// page.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\CheckAccountStatus::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'verified.ensure' => \App\Http\Middleware\EnsureEmailVerified::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'scholarship.admin.api' => \App\Http\Middleware\CheckScholarshipAdminApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


