<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // تم استدعاؤها لاستخدام دوال الـ Strings إن لزم الأمر مستقبلاً

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // نضيف شرط الحالة 'active' مباشرة في محاولة تسجيل الدخول
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['هذا الحساب معطل أو البيانات غير صحيحة.'],
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            Log::info('User logged in: ' . Auth::user()->email);

            $user = Auth::user();
            if (in_array($user->role, ['super_admin', 'scholarship_admin', 'support_admin'])) {
                return redirect('/admin/dashboard');
            }
            
            return redirect()->intended('/dashboard/student');
        }

        // إذا فشل الدخول، نفحص هل السبب أن الحساب موجود ولكنه معطل لنظهر رسالة دقيقة
        $user = User::where('email', $request->email)->first();
        if ($user && $user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['هذا الحساب معطل. يرجى التواصل مع الإدارة.'],
            ]);
        }

        Log::warning('Failed login attempt for: ' . $credentials['email']);

        throw ValidationException::withMessages([
            'email' => ['البيانات المدخلة غير صحيحة.'],
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country' => ['nullable', 'string', 'max:255'],
            'field' => ['nullable', 'string', 'max:255'],
            'high_school_gpa' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // 🎯 1. جلب معرف الشخص الداعي من الـ Session إذا كان موجوداً
        $referrerId = session()->get('referrer_id');

        // 🎁 هدية حملة نتائج التوجيهي (100 XP) - العلم مخزّن بالسيشين من GET /register?tawjihi_gift=1
        $tawjihiGift = session()->get('tawjihi_gift', false);

        // 🎯 2. إنشاء المستخدم الجديد وربطه بحقول الـ XP والإحالة
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'country' => $data['country'] ?? null,
            'field_of_study' => $data['field'] ?? null,
            'high_school_gpa' => $data['high_school_gpa'] ?? null,
            'referred_by' => $referrerId, // تخزين الـ ID الخاص بالصديق الداعي أو null
            'xp' => 0,                    // يبدأ الطالب الجديد بـ 0 نقاط
            'email_verified_at' => null,
        ]);

        // 🎯 3. ما بنمنح نقاط الداعي فوراً - لازم المدعو يكمل 50% من ملفه الشخصي
        // أولاً (يتحقق في XpService::checkReferralReward عند تحديث الملف أو رفع
        // مستند)، لمنع إنشاء حسابات وهمية فاضية لكسب نقاط بدون أي استخدام حقيقي.
        if ($referrerId) {
            session()->forget('referrer_id');
        }

        // 🎁 منح هدية حملة التوجيهي فوراً هنا (وليس بعد تسجيل الدخول)، لأن الطالب
        // بيتسجل خروجه حالاً وما بيقدر يدخل قبل توثيق إيميله.
        if ($tawjihiGift) {
            session()->forget('tawjihi_gift');
            if (!empty($data['high_school_gpa'])) {
                (new \App\Services\XpService())->award($user, 100, 'هدية تسجيل حملة نتائج التوجيهي');
            }
        }

        // Logout user (must verify email)
        Auth::logout();

        // Send verification email (don't let a mail-delivery failure crash registration
        // itself - the account is already created at this point)
        try {
            $user->notify(new \App\Notifications\VerifyEmailSignedUrlNotification($user));
        } catch (\Exception $e) {
            Log::error('Failed to send verification email to ' . $user->email . ': ' . $e->getMessage());
        }

        // Redirect to a success page (no error message)
        return redirect()->route('verification.sent');
    }

    public function logout(Request $request)
    {
        Log::info('User logged out: ' . Auth::user()->email);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}