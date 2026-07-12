<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
    <title>تم إرسال رابط التفعيل - Orbit</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    @include('layouts.partials._brand-styles')
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .floaty { animation: floaty 1.5s ease-in-out infinite; }
        @keyframes floaty { 0%,100% { transform: translateY(0);} 50% { transform: translateY(-6px);} }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-lg">
    <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 border border-slate-50">

        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 grad-bg rounded-3xl flex items-center justify-center shadow-lg floaty">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <h1 class="text-2xl font-black text-center text-slate-800 mb-2">تم إرسال رابط التفعيل</h1>
        <p class="text-center text-slate-400 font-medium mb-6">
            راجع بريدك الإلكتروني ثم اضغط على رابط التفعيل.
            <br>
            إذا لم تجد الرسالة، تحقق من تبويب الرسائل غير المرغوب بها.
        </p>

        <div class="bg-slate-50 rounded-2xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12a3 3 0 1 1 6 0c0 1.5-1.5 2.2-2 3v1H11v-1c-.5-.8-2-1.5-2-3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-slate-700 font-bold">متى أقدر أدخل الداشبورد؟</p>
                    <p class="text-slate-500 text-sm">
                        بعد تفعيل البريد الإلكتروني سيتم تحويلك مباشرة للداشبورد عند فتح الرابط.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('login') }}" class="flex-1 border-2 border-slate-100 py-3 rounded-2xl font-black text-slate-600 hover:bg-slate-50 transition text-center">
                رجوع لتسجيل الدخول
            </a>
        </div>

    </div>
</div>

</body>
</html>

