<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H7HBHJX5PF"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-H7HBHJX5PF');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
    <title>تعيين كلمة مرور جديدة - Orbit</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    <div class="relative" x-data="{ pwShow: false }">
                        <input type="password" :type="pwShow ? 'text' : 'password'" name="password" placeholder="••••••••" class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 pl-6 pr-14 outline-none transition-all font-medium text-left" dir="ltr">
                        <button type="button" @click="pwShow = !pwShow" tabindex="-1" class="absolute inset-y-0 right-4 flex items-center text-slate-300 hover:text-gold-600 transition-colors">
                            <svg x-show="!pwShow" x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="pwShow" x-cloak x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 -rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 012.132-3.411m3.132-2.507A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-1.563 3.029M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-2 mr-1">تأكيد كلمة المرور</label>
                    <div class="relative" x-data="{ pwShow: false }">
                        <input type="password" :type="pwShow ? 'text' : 'password'" name="password_confirmation" placeholder="••••••••" class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 pl-6 pr-14 outline-none transition-all font-medium text-left" dir="ltr">
                        <button type="button" @click="pwShow = !pwShow" tabindex="-1" class="absolute inset-y-0 right-4 flex items-center text-slate-300 hover:text-gold-600 transition-colors">
                            <svg x-show="!pwShow" x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="pwShow" x-cloak x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 -rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 012.132-3.411m3.132-2.507A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-1.563 3.029M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full login-gradient text-white font-black py-4 rounded-2xl shadow-xl shadow-navy-100 hover:scale-[1.02] active:scale-[0.98] transition-all text-lg">
                    تغيير كلمة المرور
                </button>
            </form>
        </div>
    </div>

</body>
</html>
