<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, int $id, string $hash)
    {
        // Check signature + expiry
        if (!$request->hasValidSignature()) {
            throw ValidationException::withMessages([
                'email' => ['رابط التفعيل غير صالح أو منتهي.'],
            ]);
        }

        /** @var User $user */
        $user = User::findOrFail($id);

        // Extra hash check
        $expectedHash = sha1($user->getEmailForVerification());
        if (!hash_equals($expectedHash, $hash)) {
            throw ValidationException::withMessages([
                'email' => ['رابط التفعيل غير صالح.'],
            ]);
        }

        if (empty($user->email_verified_at)) {
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        // If user already logged in, keep; otherwise redirect to login.
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->intended('/dashboard/student');
    }
}

