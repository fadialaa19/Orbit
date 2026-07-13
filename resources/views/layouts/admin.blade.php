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
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <title>@yield('title', 'لوحة الإدارة') - Orbit</title>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Cairo', sans-serif; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #DB8A47; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#fbfcfe] min-h-screen text-slate-700" x-data="adminLayout()">

    <aside class="fixed inset-y-0 right-0 z-50 w-72 bg-white border-l border-slate-100 flex flex-col transition-transform duration-300 lg:translate-x-0 shadow-sm"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <div class="p-7 flex items-center gap-3">
            <div class="w-11 h-11 bg-gradient-to-br from-gold-600 to-gold-400 rounded-2xl flex items-center justify-center text-white font-black shadow-lg text-lg italic">O</div>
            <div>
                <h2 class="font-black text-xl text-slate-900 leading-tight">Orbit</h2>
                <p class="text-[11px] text-gold-500 font-bold uppercase tracking-wider">لوحة الإدارة الذكية</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-2 space-y-1.5 overflow-y-auto">
            <p class="text-xs font-black text-slate-400 mb-4 mt-2 px-4 uppercase tracking-widest">القائمة الرئيسية</p>
@if(auth()->user()->role === 'super_admin' || in_array('dashboard', auth()->user()->permissions ?? []))            <a href="{{ url('/admin/dashboard') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/dashboard') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>نظرة عامة</span>
            </a>
            @endif
@if(auth()->user()->role === 'super_admin' || in_array('students', auth()->user()->permissions ?? []))            <a href="{{ url('/admin/students') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/students*') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>الطلاب</span>
                <span class="mr-auto px-2.5 py-1 bg-gold-100 text-gold-600 rounded-xl text-[10px] font-black">{{ \App\Models\User::where('role', 'student')->count() }}</span>
            </a>
            @endif
@if(auth()->user()->role === 'super_admin' || in_array('scholarships', auth()->user()->permissions ?? []))            <a href="{{ url('/admin/scholarships') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/scholarships*') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.577 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>المنح الدراسية</span>
            </a>
            @endif
            @if(auth()->user()->role === 'super_admin' || in_array('support', auth()->user()->permissions ?? []))
            <a href="{{ url('/admin/tickets') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/tickets*') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>الدعم الفني</span>
                <span class="mr-auto px-2.5 py-1 bg-rose-100 text-rose-600 rounded-xl text-[10px] font-black">{{ \App\Models\SupportTicket::where('status', 'pending')->count() }}</span>
            </a>
            @endif

<!-- <a href="{{ url('/admin/orders') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/orders*') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>طلبات الدفع</span>
                <span class="mr-auto px-2.5 py-1 bg-amber-100 text-amber-600 rounded-xl text-[10px] font-black">{{ \App\Models\Order::where('status', 'pending')->count() }}</span>
            </a>-->
@if(auth()->user()->role === 'super_admin' || in_array('contacts', auth()->user()->permissions ?? []))            <a href="{{ url('/admin/testimonials') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/testimonials*') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>تجارب الطلاب</span>
                <span class="mr-auto px-2.5 py-1 bg-amber-100 text-amber-600 rounded-xl text-[10px] font-black">{{ \App\Models\Testimonial::active()->count() }}</span>
            </a>
            @endif
@if(auth()->user()->role === 'super_admin' || in_array('admins', auth()->user()->permissions ?? []))            <a href="/admin/admins" class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm text-slate-600 hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>المدراء</span>
            </a>
@endif
            <p class="text-xs font-black text-slate-400 mb-4 mt-8 px-4 uppercase tracking-widest">النظام</p>
@if(auth()->user()->role === 'super_admin' || in_array('admins', auth()->user()->permissions ?? []))            <a href="{{ url('/admin/settings') }}"
               class="group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm transition-all {{ request()->is('admin/settings*') ? 'bg-gold-100 text-gold-600' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                <span>الإعدادات</span>
            </a>
@endif
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
            <button onclick="document.getElementById('logout-form').submit()" class="w-full group flex items-center gap-4 px-4 py-3.5 rounded-[1.3rem] font-bold text-sm text-rose-500 hover:bg-rose-50 transition-all mt-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>تسجيل الخروج</span>
            </button>
        </nav>

        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            <div class="flex items-center gap-4 bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-10 h-10 bg-gold-600 rounded-xl flex items-center justify-center text-white text-sm font-black uppercase">
                    {{ mb_substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="truncate">
                    <p class="text-xs font-black text-slate-900 leading-none">{{ Auth::user()->name ?? 'المستخدم' }}</p>
                    <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">إدارة عليا</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-h-screen lg:mr-72 transition-all">
        <header class="bg-white/80 backdrop-blur-xl border-b border-slate-100 px-8 py-4 sticky top-0 z-40 flex items-center justify-between">
            <div class="flex items-center gap-6 flex-1">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 p-2 hover:bg-slate-50 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <form action="{{ url('/admin/search') }}" method="GET" class="relative hidden md:block w-full max-w-md">
                    <input type="text" name="q" x-model="searchQuery" placeholder="بحث عن طالب، تذكرة أو عملية دفع..."
                           class="w-full bg-slate-100 border-2 border-transparent focus:bg-white focus:border-gold-100 rounded-2xl px-11 py-2.5 text-xs font-bold transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" @click.away="notificationsOpen = false"
                            class="relative p-2.5 bg-slate-50 text-slate-400 hover:text-gold-600 hover:bg-gold-100 rounded-2xl transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <div x-show="notificationsOpen" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         class="absolute left-0 mt-4 w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden z-[60]">
                        <div class="p-5 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                            <span class="font-black text-xs text-slate-900 uppercase">أحدث التنبيهات</span>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.data.link || '#'" class="p-4 hover:bg-slate-50 transition border-b border-slate-50 block group">
                                    <div class="flex justify-between items-start">
                                        <p class="text-[11px] font-black text-slate-900 group-hover:text-gold-600 transition" x-text="n.data.title"></p>
                                        <span x-show="!n.read_at" class="w-1.5 h-1.5 bg-gold-600 rounded-full"></span>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1" x-text="n.data.body"></p>
                                    <p class="text-[9px] text-gold-400 mt-2 font-black uppercase" x-text="n.created_at_human"></p>
                                </a>
                            </template>
                        </div>
                        <a href="{{ url('/admin/notifications') }}" class="block p-4 text-center text-[10px] font-black text-slate-400 hover:text-gold-600 bg-slate-50/30">عرض كافة الإشعارات</a>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-100 mx-2"></div>
                <span class="text-xs font-black text-slate-800">@yield('breadcrumb')</span>
            </div>
        </header>

        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"></div>

<script>
function adminLayout() {
    return {
        sidebarOpen: false,
        notificationsOpen: false,
        searchQuery: '',
        notifications: [],
        fetchNotifications() {
            fetch('/admin/notifications/latest')
                .then(res => res.json())
                .then(data => { this.notifications = data; })
                .catch(() => {});
        },
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 60000);
        }
    };
}
</script>
</body>
</html>