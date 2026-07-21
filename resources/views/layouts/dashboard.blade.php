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
    <link rel="icon" href="{{ asset('assets/images/logo-icon.png') . '?v=2' }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة التحكم - @yield('title', 'منحي')</title>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script defer crossorigin="anonymous" src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #DB8A47; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50" x-data="studentLayout()">

    {{-- الشريط الجانبي: ثابت وقابل للطي لأيقونات فقط على الشاشات الكبيرة،
         وقائمة منزلقة كاملة على الجوال (نفس نمط لوحة الأدمن). --}}
    <aside class="fixed inset-y-0 right-0 z-50 bg-white border-l border-slate-100 flex flex-col transition-transform duration-300 lg:translate-x-0 shadow-sm w-72"
           :class="[sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-20' : 'lg:w-72']">

        <div class="p-5 flex items-center gap-3" :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
            <a href="/" class="shrink-0"><img src="{{ asset('assets/images/logo-icon.png') . '?v=2' }}" class="h-10 w-10 rounded-full object-cover"></a>
            <div x-show="!sidebarCollapsed">
                <h2 class="font-black text-lg text-slate-900 leading-tight">Orbit</h2>
                <p class="text-[10px] text-gold-500 font-bold uppercase tracking-wider">لوحة الطالب</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-2 space-y-1.5 overflow-y-auto overflow-x-hidden">
            @php
                $navLinks = [
                    ['route' => 'dashboard.student', 'is' => 'dashboard/student', 'label' => 'لوحة التحكم', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'dashboard.scholarships', 'is' => 'dashboard/scholarships*', 'label' => 'المنح', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.577 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['route' => 'dashboard.favorites', 'is' => 'dashboard/favorites*', 'label' => 'المفضلات', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    ['route' => 'dashboard.communications', 'is' => 'dashboard/communications*', 'label' => 'مركز التواصل', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                    ['route' => 'dashboard.community', 'is' => 'dashboard/community*', 'label' => 'المجتمع', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['route' => 'dashboard.document-requests', 'is' => 'dashboard/document-requests*', 'label' => 'الأوراق الرسمية', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'dashboard.testimonial', 'is' => 'dashboard/testimonial*', 'label' => 'شارك تجربتك', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                    ['route' => 'dashboard.xp', 'is' => 'dashboard/xp*', 'label' => 'نقاطي', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ];
            @endphp

            @foreach($navLinks as $link)
            <div class="relative" x-data="{ hovering: false }" @mouseenter="hovering = true" @mouseleave="hovering = false">
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-4 px-4 py-3 rounded-[1.1rem] font-bold text-sm transition-all {{ request()->is($link['is']) ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <svg class="w-5 h-5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                    <span x-show="!sidebarCollapsed">{{ $link['label'] }}</span>
                </a>
                {{-- تلميح يظهر بجانب الأيقونة فقط لما القائمة تكون مطوية على شاشة كبيرة --}}
                <span x-show="sidebarCollapsed && hovering" x-cloak
                      class="hidden lg:block pointer-events-none absolute top-1/2 -translate-y-1/2 right-full mr-3 px-3 py-1.5 bg-slate-900 text-white text-[11px] font-bold rounded-lg whitespace-nowrap z-50">
                    {{ $link['label'] }}
                </span>
            </div>
            @endforeach

            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
            <div class="relative mt-4" x-data="{ hovering: false }" @mouseenter="hovering = true" @mouseleave="hovering = false">
                <button onclick="document.getElementById('logout-form').submit()"
                        class="w-full flex items-center gap-4 px-4 py-3 rounded-[1.1rem] font-bold text-sm text-rose-500 hover:bg-rose-50 transition-all"
                        :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span x-show="!sidebarCollapsed">تسجيل الخروج</span>
                </button>
                <span x-show="sidebarCollapsed && hovering" x-cloak
                      class="hidden lg:block pointer-events-none absolute top-1/2 -translate-y-1/2 right-full mr-3 px-3 py-1.5 bg-slate-900 text-white text-[11px] font-bold rounded-lg whitespace-nowrap z-50">
                    تسجيل الخروج
                </span>
            </div>
        </nav>

        {{-- زر طي/فتح القائمة - شاشات كبيرة فقط --}}
        <button @click="toggleCollapse()" class="hidden lg:flex items-center justify-center gap-2 p-4 border-t border-slate-50 text-slate-400 hover:text-gold-600 hover:bg-slate-50 transition-all font-bold text-xs">
            <svg class="w-4 h-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            <span x-show="!sidebarCollapsed">طي القائمة</span>
        </button>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"></div>

    <div class="flex flex-col min-h-screen" :class="sidebarCollapsed ? 'lg:mr-20' : 'lg:mr-72'">
        <header class="bg-white/80 backdrop-blur-xl border-b border-slate-100 px-4 md:px-8 py-3 sticky top-0 z-30 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 p-2 hover:bg-slate-50 rounded-xl transition shrink-0" aria-label="فتح القائمة">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="hidden md:block flex-1 max-w-md">
                    @yield('header_search')
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard.notifications') }}" class="relative text-slate-400 hover:text-gold-600 transition p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2"/></svg>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </a>

                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-3 group cursor-pointer">
                        <div class="text-left hidden sm:block">
                            <p class="text-xs font-black text-slate-800 leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 font-bold mt-1 text-right italic">طالب</p>
                        </div>
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-2xl object-cover shadow-lg">
                        @else
                            <div class="w-10 h-10 bg-gradient-to-tr from-gold-600 to-gold-400 rounded-2xl flex items-center justify-center text-white font-black shadow-lg">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div x-show="open" @click.away="open = false" x-cloak x-transition
                         class="absolute left-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                        <a href="{{ route('dashboard.profile') }}" class="flex items-center justify-end gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-gold-600 transition">
                            <span>الملف الشخصي</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                        </a>
                        <a href="{{ route('dashboard.settings') }}" class="flex items-center justify-end gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-gold-600 transition">
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
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>

    @include('layouts.partials._toast')

@include('components.groq-chat')

<script>
    function studentLayout() {
        return {
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('studentSidebarCollapsed') === 'true',
            toggleCollapse() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('studentSidebarCollapsed', this.sidebarCollapsed);
            },
        };
    }

    // نبضة XP لمكافأة وقت التصفح الفعلي بالموقع - كل 5 دقائق طالما الصفحة مرئية ونشطة
    (function () {
        function sendHeartbeat() {
            if (document.visibilityState !== 'visible') return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) return;
            fetch('{{ route('dashboard.xp-heartbeat') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
            }).catch(() => {});
        }
        sendHeartbeat();
        setInterval(sendHeartbeat, 5 * 60 * 1000);
    })();
</script>

</body>
</html>
