<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
{
    if (Auth::check()) {
        // جلب بيانات المستخدم طازجة من قاعدة البيانات
        $user = \App\Models\User::find(Auth::id());

        if (!$user || $user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('error', 'تم تعطيل حسابك تلقائياً.');
        }
    }

    return $next($request);
}
}