<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'تعذّر إرسال رابط الاستعادة حالياً، يرجى المحاولة لاحقاً.']);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'تم تغيير كلمة المرور بنجاح، يمكنك تسجيل الدخول الآن.')
            : back()->withErrors(['email' => __($status)]);
    }
}
