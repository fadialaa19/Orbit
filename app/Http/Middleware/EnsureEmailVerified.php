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

        if ($user && empty($user->email_verified_at)) {
            return redirect()->route('login')->withErrors([
                'email' => 'حسابك غير مفعل بعد. يرجى تفعيل البريد الإلكتروني.',
            ]);
        }

        return $next($request);
    }
}

