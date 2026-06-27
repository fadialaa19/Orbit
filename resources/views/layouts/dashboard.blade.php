<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة التحكم - @yield('title', 'منحي')</title>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script defer crossorigin="anonymous" src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50">

<nav class="bg-white border-b border-slate-100 py-3 px-8 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        
        <div class="flex items-center gap-10">
            <a href="/"><img src="{{ asset('assets/images/logo.png') }}" class="h-10"></a>
            
            <div class="hidden md:flex gap-6 text-sm font-bold text-slate-600">
                <a href="{{ route('dashboard.student') }}" class="{{ request()->routeIs('dashboard.student') ? 'text-indigo-600' : 'hover:text-indigo-500' }} transition">لوحة التحكم</a>
                <a href="{{ route('dashboard.scholarships') }}" class="{{ request()->routeIs('dashboard.scholarships') ? 'text-indigo-600' : 'hover:text-indigo-500' }} transition">المنح</a>
<!--  <a href="{{ route('dashboard.applications') }}" class="{{ request()->routeIs('dashboard.applications') ? 'text-indigo-600' : 'hover:text-indigo-500' }} transition">طلباتي</a>
                <a href="{{ route('dashboard.services') }}" class="{{ request()->routeIs('dashboard.services') ? 'text-indigo-600' : 'hover:text-indigo-500' }} transition">الخدمات</a> -->
                <a href="{{ route('dashboard.favorites') }}" class="{{ request()->routeIs('dashboard.favorites') ? 'text-indigo-600' : 'hover:text-indigo-500' }} transition">المفضلات</a>
                <a href="{{ route('dashboard.communications') }}" class="{{ request()->routeIs('dashboard.communications') ? 'text-indigo-600' : 'hover:text-indigo-500' }} transition">مركز التواصل</a>

            </div>
        </div>

        <div class="flex-1 flex justify-center px-10">
            @yield('header_search')
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4 border-l pl-6">
                <a href="{{ route('dashboard.notifications') }}" class="relative text-slate-400 hover:text-indigo-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2"/></svg>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </a>
            </div>
            
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 group cursor-pointer">
                    <div class="text-left hidden sm:block">
                        <p class="text-xs font-black text-slate-800 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1 text-right italic">طالب</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-purple-500 rounded-2xl flex items-center justify-center text-white font-black shadow-lg">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>

                <div x-show="open" @click.away="open = false" x-cloak x-transition
                     class="absolute left-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                    <a href="{{ route('dashboard.profile') }}" class="flex items-center justify-end gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                        <span>الملف الشخصي</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                    </a>
                    <a href="{{ route('dashboard.settings') }}" class="flex items-center justify-end gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                        <span>الإعدادات</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2"/></svg>
                    </a>
                    <div class="border-t border-slate-50 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-end gap-3 px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 transition">
                            <span>تسجيل الخروج</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<main>
    @yield('content')
    </main>

    @include('layouts.partials._toast')

@include('components.groq-chat')

<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    // إعداد الاتصال بـ Reverb
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ env("REVERB_APP_KEY") }}',
        wsHost: '127.0.0.1',
        wsPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });
</script>
</body>
</html>
