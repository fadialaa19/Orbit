<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Staff accounts are created internally by a trusted admin, not via
        // public self-registration, so they're never subject to the
        // email-ownership check this middleware exists to enforce.
        $staffRoles = ['super_admin', 'scholarship_admin', 'support_admin'];
        if ($user && in_array($user->role, $staffRoles, true)) {
            return $next($request);
        }

        if ($user && empty($user->email_verified_at)) {
            return redirect()->route('login')->withErrors([
                'email' => 'حسابك غير مفعل بعد. يرجى تفعيل البريد الإلكتروني.',
            ]);
        }

        return $next($request);
    }
}

