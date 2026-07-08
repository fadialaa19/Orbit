<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Orbit ☕️</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .login-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
        }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full">
        <div class="flex justify-end mb-6">
            <a href="/" class="flex items-center gap-2 text-slate-600 hover:text-indigo-600 font-bold transition">
                <span>العودة للرئيسية</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 p-10 border border-slate-50 relative overflow-hidden">
            
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 login-gradient rounded-3xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-12 group hover:rotate-0 transition-transform duration-500">
                    <img src="{{ asset('assets/images/logo.png') }}" class="w-12 h-12 -rotate-12 group-hover:rotate-0 transition-transform" alt="Logo">
                </div>
            </div>

            <h1 class="text-3xl font-black text-center text-slate-800 mb-2">مرحباً بعودتك!</h1>
            <p class="text-center text-slate-400 mb-10 font-medium">قم بتسجيل الدخول للمتابعة</p>

            <div class="space-y-3 mb-8">
<a href="{{ route('auth.google.redirect') }}" class="w-full border-2 border-slate-100 py-3 rounded-2xl flex items-center justify-center gap-3 font-bold text-slate-600 hover:bg-slate-50 transition">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                    تسجيل الدخول بواسطة Google
                </a>

            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-slate-300 font-bold text-sm">أو</span>
                <div class="h-px bg-slate-100 flex-1"></div>
            </div>

<form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2 mr-1">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" name="email" placeholder="your.email@example.com" class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl py-4 px-6 outline-none transition-all font-medium text-left" dir="ltr">
                        <span class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </span>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <label class="text-slate-700 font-bold">كلمة المرور</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" placeholder="••••••••" class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl py-4 px-6 outline-none transition-all font-medium text-left" dir="ltr">
                        <span class="absolute inset-y-0 right-4 flex items-center text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                    <a href="{{ route('password.request') }}" class="text-indigo-600 text-sm font-bold hover:underline">نسيت كلمة المرور؟</a>

                </div>

                <button type="submit" class="w-full login-gradient text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-200 hover:scale-[1.02] active:scale-[0.98] transition-all text-lg">
                    تسجيل الدخول
                </button>
            </form>

            <p class="text-center mt-10 text-slate-500 font-bold">
                ليس لديك حساب؟ 
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">إنشاء حساب جديد</a>
            </p>
        </div>
    </div>

</body>
</html>