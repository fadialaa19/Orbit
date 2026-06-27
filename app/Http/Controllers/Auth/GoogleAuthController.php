<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            /** @var SocialiteUser $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'تعذر تسجيل الدخول عبر Google. حاول مرة أخرى.']);
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'حساب Google لا يحتوي على بريد إلكتروني.']);
        }

        // Upsert user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Google User'),
                'email' => $email,
                'password' => bcrypt(
                    bin2hex(random_bytes(16))
                ),
                'country' => null,
                'field' => null,
                // Activate immediately per your chosen policy
                'email_verified_at' => now(),
                // Keep defaults for role/status if migration provides defaults
            ]);
        } else {
            // Ensure verified on subsequent logins via Google
            if (empty($user->email_verified_at)) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        }

        Auth::login($user);

        if (in_array($user->role, ['super_admin', 'scholarship_admin', 'support_admin'], true)) {
            return redirect('/admin/dashboard');
        }

        return redirect('/dashboard/student');
    }
}

