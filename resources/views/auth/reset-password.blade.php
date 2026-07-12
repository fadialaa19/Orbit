<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعيين كلمة مرور جديدة - Orbit</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    @include('layouts.partials._brand-styles')
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full">
        <div class="flex justify-end mb-6">
            <a href="/" class="flex items-center gap-2 text-slate-600 hover:text-gold-600 font-bold transition">
                <span>العودة للرئيسية</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gold-100/50 p-10 border border-slate-50 relative overflow-hidden">

            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 flex items-center justify-center">
                    <img src="{{ asset('assets/images/logo.png') }}" class="w-20 h-20 object-contain" alt="Logo">
                </div>
            </div>

            <h1 class="text-3xl font-black text-center text-slate-800 mb-2">تعيين كلمة مرور جديدة</h1>
            <p class="text-center text-slate-400 mb-10 font-medium">أدخل كلمة المرور الجديدة لحسابك</p>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-slate-700 font-bold mb-2 mr-1">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email', $email) }}" placeholder="your.email@example.com" class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-6 outline-none transition-all font-medium text-left" dir="ltr">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-2 mr-1">كلمة المرور الجديدة</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-6 outline-none transition-all font-medium text-left" dir="ltr">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-2 mr-1">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-6 outline-none transition-all font-medium text-left" dir="ltr">
                </div>

                <button type="submit" class="w-full login-gradient text-white font-black py-4 rounded-2xl shadow-xl shadow-navy-100 hover:scale-[1.02] active:scale-[0.98] transition-all text-lg">
                    تغيير كلمة المرور
                </button>
            </form>
        </div>
    </div>

</body>
</html>
