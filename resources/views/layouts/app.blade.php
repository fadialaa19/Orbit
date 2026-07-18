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
    <link rel="icon" href="{{ asset('assets/images/logo-icon.png') . '?v=1' }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Orbit - منصتك للحصول على منح دراسية حول العالم')</title>

    <meta name="description" content="@yield('meta_description', 'Orbit منصة عربية تساعد الطلاب على اكتشاف المنح الدراسية حول العالم والتقديم عليها بسهولة، بدعم من الذكاء الاصطناعي.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Orbit">
    <meta property="og:title" content="@yield('title', 'Orbit - منصتك للحصول على منح دراسية حول العالم')">
    <meta property="og:description" content="@yield('meta_description', 'Orbit منصة عربية تساعد الطلاب على اكتشاف المنح الدراسية حول العالم والتقديم عليها بسهولة، بدعم من الذكاء الاصطناعي.')">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') . '?v=2' }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2130498782125345" crossorigin="anonymous"></script>

    <script defer crossorigin="anonymous" src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

    @include('layouts.partials._brand-styles')
    <style>
        html, body { font-family: 'Cairo', sans-serif; overflow-x: hidden; max-width: 100%; }
        .bg-main { background: #DB8A47; }
        [x-cloak] { display: none !important; }
        /* أنيميشن AOS كان بيعلق أحياناً بحالته الأولية (شفاف ومزاح عن مكانه) بسبب
           تعارض CSS layers بين Tailwind v4 وستايل AOS غير المُطبَّق بطبقة -
           هاد التوكيد الصريح بيضمن رجوع العنصر لحالته الطبيعية دايماً بعد التفعيل. */
        [data-aos].aos-animate {
            opacity: 1 !important;
            transform: none !important;
        }
    </style>
</head>
<body class="bg-gray-50 text-right">

    <nav x-data="{ mobileNavOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
         :class="scrolled ? 'bg-white shadow-md' : 'bg-white/80 backdrop-blur-md shadow-sm'"
         class="py-4 px-8 sticky top-0 z-50 transition-all duration-300">
        <div class="flex justify-between items-center">
        <div class="flex items-center gap-6">
        <a href="/" class="flex items-center gap-2">
            <img src="{{ asset('assets/images/logo-icon.png') . '?v=1' }}" alt="Orbit Logo" class="h-12 w-12 rounded-full object-cover transition-transform duration-300 hover:scale-105">
            <span class="font-bold text-xl text-slate-800 hidden md:block">Orbit</span>
        </a>

        <div class="hidden md:flex gap-6 text-gray-600 font-medium">
            <a href="/" class="hover:text-gold-600 transition">الرئيسية</a>
            <a href="{{ route('guest.scholarships') }}" class="hover:text-gold-600 transition">المنح الدراسية</a>
            <a href="{{ route('guest.about') }}" class="hover:text-gold-600 transition">عن المنصة</a>
            <a href="{{ route('guest.services') }}" class="hover:text-gold-600 transition">الخدمات</a>
            <a href="{{ route('guest.contact') }}" class="hover:text-gold-600 transition">تواصل معنا</a>

        </div>
    </div>
        <div class="hidden md:flex items-center gap-4">
@auth
                <a href="{{ route('dashboard.student') }}" class="flex items-center gap-2 font-bold hover:text-gold-600 transition">
                    لوحة التحكم <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543 .94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543 .826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-bold hover:text-gold-600 transition">تسجيل الخروج</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="bg-main text-white px-6 py-2 rounded-full hover:bg-gold-700">إنشاء حساب</a>
            @endauth
        </div>

        <button @click="mobileNavOpen = !mobileNavOpen" class="md:hidden p-2 text-slate-600" aria-label="فتح القائمة">
            <svg x-show="!mobileNavOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            <svg x-show="mobileNavOpen" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        </div>

        <div x-show="mobileNavOpen" x-cloak x-transition class="md:hidden mt-4 pb-2 space-y-3 text-gray-600 font-medium border-t border-slate-100 pt-4">
            <a href="/" class="block hover:text-gold-600 transition">الرئيسية</a>
            <a href="{{ route('guest.scholarships') }}" class="block hover:text-gold-600 transition">المنح الدراسية</a>
            <a href="{{ route('guest.about') }}" class="block hover:text-gold-600 transition">عن المنصة</a>
            <a href="{{ route('guest.services') }}" class="block hover:text-gold-600 transition">الخدمات</a>
            <a href="{{ route('guest.contact') }}" class="block hover:text-gold-600 transition">تواصل معنا</a>
            <div class="border-t border-slate-100 pt-3 flex flex-col gap-3">
@auth
                <a href="{{ route('dashboard.student') }}" class="font-bold hover:text-gold-600 transition">لوحة التحكم</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-bold hover:text-gold-600 transition">تسجيل الخروج</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="inline-block bg-main text-white px-6 py-2 rounded-full hover:bg-gold-700 w-fit">إنشاء حساب</a>
            @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-navy-950 text-white py-12 px-8 mt-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-7xl mx-auto">
            <div>
                <img src="{{ asset('assets/images/logo-icon.png') . '?v=1' }}" alt="Logo" class="h-12 w-12 rounded-full object-cover mb-6"> 
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
                    <li><a href="{{ route('guest.contact') }}" class="hover:text-white transition">اتصل بنا</a></li>
                </ul>
            </div>
            <div>
                @php
                    $contactEmail = \App\Models\Setting::get('contact_email', 'orbit.ships@gmail.com');
                    $contactPhone = \App\Models\Setting::get('contact_phone', '+970 59 270 4945');
                    $socialLinks = [
                        'whatsapp_url' => ['label' => 'واتساب', 'path' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z M12 2C6.477 2 2 6.477 2 12c0 1.821.487 3.53 1.338 5L2 22l5.2-1.304A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z'],
                        'facebook_url' => ['label' => 'فيسبوك', 'path' => 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036 26.805 26.805 0 00-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 00-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.628-5.373-12-12-12s-12 5.372-12 12c0 5.628 3.874 10.35 9.101 11.647z'],
                        'instagram_url' => ['label' => 'انستقرام', 'path' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.332.014 7.052.072 2.695.272.273 2.69.073 7.052.014 8.332 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.332 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.668-.072-4.948-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'],
                        'telegram_url' => ['label' => 'تيليجرام', 'path' => 'M23.91 3.79L20.3 20.84c-.25 1.21-.98 1.5-1.99.94l-5.5-4.07-2.66 2.57c-.3.3-.55.55-1.1.55l.4-5.56 10.1-9.15c.44-.39-.1-.6-.68-.22L7.72 13.62l-5.44-1.7c-1.18-.37-1.2-1.18.26-1.75L22.46 2.1c.99-.36 1.85.24 1.45 1.7z'],
                    ];
                @endphp
                <h4 class="font-bold mb-4">تواصل معنا</h4>
                <p class="text-gray-400" dir="ltr">{{ $contactEmail }}</p>
                <p class="text-gray-400" dir="ltr">{{ $contactPhone }}</p>
                <div class="flex items-center gap-3 mt-4">
                    @foreach($socialLinks as $key => $social)
                        @if(\App\Models\Setting::get($key))
                            <a href="{{ \App\Models\Setting::get($key) }}" target="_blank" rel="noopener noreferrer" title="{{ $social['label'] }}"
                               class="w-9 h-9 bg-white/10 hover:bg-gold-500 rounded-full flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $social['path'] }}"/></svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-500 text-xs space-y-1">
            <p>© 2026 Orbit. جميع الحقوق محفوظة.</p>
            <p>Illustration by <a href="https://storyset.com" target="_blank" rel="noopener" class="hover:text-white transition">Storyset</a></p>
        </div>
    </footer>


    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true,
  });
  // خط Cairo والصور بيوصلوا بعد أول init، وهاد بيخلي حسابات AOS لمواقع
  // العناصر تصير قديمة فيبقى العنصر عالق بحالته قبل الأنيميشن (شفاف وبمكانه
  // الأصلي المزاح) - إعادة الحساب بعد اكتمال التحميل بيحل المشكلة نهائياً.
  window.addEventListener('load', () => AOS.refresh());
</script>

@include('components.groq-chat')
</body>
</html>
