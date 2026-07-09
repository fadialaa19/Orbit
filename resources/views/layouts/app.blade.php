<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orbit ☕️</title>

    <script defer crossorigin="anonymous" src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        body { font-family: 'Cairo', sans-serif; }
        .grad-text { background: linear-gradient(90deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bg-main { background: #6366f1; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-right">

    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-6">
        <a href="/" class="flex items-center gap-2">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Orbit Logo" class="h-12 w-auto transition-transform duration-300 hover:scale-105">
            <span class="font-bold text-xl text-slate-800 hidden md:block">Orbit ☕️</span>
        </a>
        
        <div class="hidden md:flex gap-6 text-gray-600 font-medium">
            <a href="/" class="hover:text-indigo-600 transition">الرئيسية</a>
            <a href="{{ route('guest.scholarships') }}" class="hover:text-indigo-600 transition">المنح الدراسية</a>
            <a href="{{ route('guest.about') }}" class="hover:text-indigo-600 transition">عن المنصة</a>
            <a href="{{ route('guest.services') }}" class="hover:text-indigo-600 transition">الخدمات</a>

        </div>
    </div>
        <div class="flex items-center gap-4">
@auth
                <a href="{{ route('dashboard.student') }}" class="flex items-center gap-2 font-bold hover:text-indigo-600 transition">
                    لوحة التحكم <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543 .94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543 .826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-bold hover:text-indigo-600 transition">تسجيل الخروج</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="bg-main text-white px-6 py-2 rounded-full hover:bg-indigo-700">إنشاء حساب</a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-[#0f172a] text-white py-12 px-8 mt-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-7xl mx-auto">
            <div>
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-12 w-auto mb-6"> 
                <p class="text-gray-400">منصة رائدة لمساعدة الطلاب للحصول على منح دراسية حول العالم بكل حرية وبدون قيود.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">روابط سريعة</h4>
                <ul class="text-gray-400 space-y-2">
                    <li><a href="{{ route('guest.scholarships') }}" class="hover:text-white transition">المنح الدراسية</a></li>
                    <li><a href="{{ route('guest.services') }}" class="hover:text-white transition">الخدمات</a></li>
                    <li><a href="{{ route('guest.about') }}" class="hover:text-white transition">عن المنصة</a></li>

                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">الدعم</h4>
                <ul class="text-gray-400 space-y-2">
                    <li>مركز المساعدة</li>
                    <li>الأسئلة الشائعة</li>
                    <li>اتصل بنا</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">تواصل معنا</h4>
                <p class="text-gray-400">khaledelhobe@gmail.com</p>
                <p class="text-gray-400" dir="ltr">+970 59 270 4945</p>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-500">
            © 2026 Orbit ☕️. جميع الحقوق محفوظة.
        </div>
    </footer>


    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true,
  });
</script>

@include('components.groq-chat')
</body>
</html>
