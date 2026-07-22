@extends('layouts.app')

@section('content')
<style>
    /* لمسة إضافية للزر لجذب الانتباه */
    @keyframes pulse-gold {
        0% { box-shadow: 0 0 0 0 rgba(219, 138, 71, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(219, 138, 71, 0); }
        100% { box-shadow: 0 0 0 0 rgba(219, 138, 71, 0); }
    }
    .btn-pulse { animation: pulse-gold 2s infinite; }
</style>

<section class="py-20 px-8 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center overflow-hidden">
    <div data-aos="fade-left" data-aos-duration="1200">
        <span class="bg-gold-100 text-gold-600 px-5 py-2 rounded-full text-sm font-bold border border-gold-100 inline-block mb-6">
            ✨ مدعوم بالذكاء الاصطناعي
        </span>
        <h1 class="text-6xl font-extrabold mt-2 leading-[1.2] text-slate-900">
            ابدأ رحلتك <span class="grad-text">الدراسية</span> <br> نحو العالم
        </h1>
        <p class="text-gray-500 mt-8 text-xl leading-relaxed max-w-lg">
            اكتشف آلاف المنح الدراسية المتاحة حول العالم وقدم طلباتك بسهولة مع خدماتنا المتخصصة.
        </p>
        <div class="flex flex-wrap gap-5 mt-12">
            <a href="{{ route('guest.scholarships') }}" class="bg-gold-600 text-white px-10 py-4 rounded-2xl shadow-xl flex items-center gap-3 hover:bg-gold-700 transition-all duration-300 transform hover:-translate-y-1 btn-pulse">
                <span class="font-bold">استكشف المنح</span>
                <span class="text-xl">←</span>
            </a>
            <a href="{{ route('register') }}" class="border-2 border-slate-200 px-10 py-4 rounded-2xl font-bold text-slate-700 hover:bg-slate-50 transition-all duration-300">
                ابدأ الآن مجاناً
            </a>
        </div>
    </div>
    
    <div class="relative py-10">
        {{-- إطار مرجعي بنفس عرض الفيديو بالضبط، حتى تتموضع الكاردات العائمة
             بالنسبة لحواف الفيديو نفسه وليس بالنسبة لعرض العمود الأوسع -
             هيك ما بتنكسر وتتراكب فوق الفيديو لما يضيق العمود بمقاسات الشاشة المتوسطة. --}}
        <div class="relative max-w-[220px] mx-auto">
            {{-- توهج خفيف خلف الإطار بنفس الألوان الذهبية الدافئة لإضاءة الفيديو --}}
            <div class="absolute -inset-6 bg-gradient-to-br from-gold-300/40 via-gold-200/25 to-transparent blur-2xl rounded-full pointer-events-none"></div>

            <div role="img" aria-label="مساعد أوربيت الذكي" class="relative z-10 rounded-[2rem] overflow-hidden shadow-[0_25px_60px_-15px_rgba(219,138,71,0.4)] transition-transform duration-700 hover:rotate-2 group">
                <video id="orbitMascotVideo" autoplay muted loop playsinline disablepictureinpicture controlslist="nodownload noplaybackrate nofullscreen" oncontextmenu="return false" class="w-full h-auto block transition-all duration-500 group-hover:scale-[1.02]">
                    <source src="{{ asset('assets/videos/orbit-ai-mascot.mp4') }}" type="video/mp4">
                </video>
            </div>

            <div class="absolute top-2 right-2 sm:right-auto sm:left-0 sm:-translate-x-[calc(100%+14px)] bg-white p-3 sm:p-4 rounded-3xl shadow-2xl border border-gold-100 flex items-center gap-2.5 sm:gap-3 z-20">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gold-600 rounded-xl flex items-center justify-center text-white font-bold text-xs sm:text-sm shrink-0">95%</div>
                <div>
                    <p class="text-[8px] sm:text-[9px] text-gray-400 font-black uppercase tracking-wider text-right">نسبة التطابق</p>
                    <p class="text-[11px] sm:text-xs font-bold text-slate-800">ذكاء اصطناعي</p>
                </div>
            </div>

            <div class="absolute bottom-2 left-2 sm:left-auto sm:right-0 sm:translate-x-[calc(100%+14px)] bg-white p-3 sm:p-4 rounded-3xl shadow-2xl border border-navy-100 flex items-center gap-2.5 sm:gap-3 z-20 max-w-[200px] sm:max-w-[180px]">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-navy-900 rounded-xl flex items-center justify-center text-white shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] sm:text-xs font-black text-slate-800">الأستاذة نور</p>
                    <p class="text-[8px] sm:text-[9px] text-gray-400">مستشارة ذكاء اصطناعي ٢٤/٧</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20 relative z-0">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12 px-8">
        @php
            // أرقام حقيقية 100% محسوبة من قاعدة البيانات (وليست وهمية)، تُحدَّث تلقائياً
            $statCards = [
                ['value' => $stats['students'], 'label' => 'طالب مسجل', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4'],
                ['value' => $stats['scholarships'], 'label' => 'منحة متاحة', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-4 6h16v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8z'],
                ['value' => $stats['successStories'], 'label' => 'قصة نجاح', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.539-1.118l1.519-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                ['value' => $stats['countries'], 'label' => 'دولة حول العالم', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];

            $formatStat = function ($n) {
                if ($n >= 1000) {
                    return '+' . rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'k';
                }
                return $n > 0 ? '+' . $n : $n;
            };
        @endphp
        @foreach($statCards as $index => $stat)
        <div data-aos="fade-up" data-aos-delay="{{ $index * 100 }}" class="text-center group cursor-default">
            <div class="text-gold-600 bg-gold-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gold-600 group-hover:text-white transition-all duration-500 transform group-hover:rotate-12 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-black text-slate-900 mb-2">{{ $formatStat($stat['value']) }}</h2>
            <p class="text-gray-400 font-medium">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<section class="py-24 px-8 bg-slate-50 text-center relative overflow-hidden">
    <div class="relative z-10 mb-20">
        <h2 data-aos="fade-up" class="text-4xl font-black text-slate-900 mb-4">كيف يعمل؟</h2>
        <div data-aos="fade-up" data-aos-delay="200" class="w-24 h-1.5 bg-gold-600 mx-auto rounded-full mb-6"></div>
        <p data-aos="fade-up" data-aos-delay="300" class="text-gray-500 text-lg max-w-xl mx-auto">ثلاث خطوات بسيطة تبعدك عن تحقيق حلمك الدراسي.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-6xl mx-auto">
        @foreach([
            ['1', 'ابحث عن المنحة المثالية', 'استخدم محرك البحث المتقدم المفلتر حسب تخصصك ودولتك.'],
            ['2', 'قدم طلبك بسهولة', 'مساعدنا الذكي يوجهك خطوة بخطوة لصياغة خطاب النوايا.'],
            ['3', 'تتبع تقدمك', 'لوحة تحكم خاصة بك لمتابعة حالة القبول في الوقت الفعلي.']
        ] as $index => $step)
        <div data-aos="fade-up" data-aos-delay="{{ $index * 200 }}" 
             class="bg-white p-12 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-4 group">
            <div class="w-20 h-20 bg-slate-50 text-gold-600 rounded-[1.5rem] flex items-center justify-center mx-auto mb-8 text-3xl font-black group-hover:bg-gold-600 group-hover:text-white transition-all duration-500 shadow-inner">
                {{ $step[0] }}
            </div>
            <h3 class="font-black text-xl mb-4 text-slate-800">{{ $step[1] }}</h3>
            <p class="text-gray-400 leading-relaxed text-sm">{{ $step[2] }}</p>
        </div>
        @endforeach
    </div>
</section>

<section id="team" class="py-24 px-8 bg-white scroll-mt-20">
    <div class="text-center mb-16">
        <h2 data-aos="fade-up" class="text-4xl font-black text-slate-900 mb-4">فريقنا</h2>
        <div data-aos="fade-up" data-aos-delay="200" class="w-24 h-1.5 bg-gold-600 mx-auto rounded-full mb-6"></div>
        <p data-aos="fade-up" data-aos-delay="300" class="text-gray-500 text-lg max-w-xl mx-auto">الأشخاص وراء منصة Orbit</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        @php
            $roleLabels = [
                'super_admin' => 'مؤسس ومدير تنفيذي',
                'scholarship_admin' => 'خبير منح دراسية',
                'support_admin' => 'مدير دعم فني',
            ];
            $roleColors = [
                'super_admin' => 'from-gold-600 to-gold-400',
                'scholarship_admin' => 'from-emerald-500 to-green-500',
                'support_admin' => 'from-orange-500 to-amber-500',
            ];
            $roleBios = [
                'super_admin' => 'يقود المنصة نحو تمكين آلاف الطلاب من تحقيق أحلامهم الدراسية حول العالم.',
                'scholarship_admin' => 'خبير في مجال المنح الدولية وشراكات الجامعات العالمية ودعم الطلاب.',
                'support_admin' => 'مختص في تطوير حلول الدعم الفني وتحسين تجربة المستخدم على المنصة.',
            ];
        @endphp

        @foreach($teamMembers as $member)
        <div data-aos="fade-up" class="bg-slate-50 p-8 rounded-[2rem] hover:bg-white hover:shadow-xl transition-all duration-500 text-center">
            @if($member->avatar)
                <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-20 h-20 rounded-full object-cover shadow-lg mx-auto mb-5">
            @else
                <div class="w-20 h-20 bg-gradient-to-br {{ $roleColors[$member->role] ?? 'from-gold-600 to-gold-400' }} rounded-full flex items-center justify-center text-white font-black text-2xl shadow-lg mx-auto mb-5">
                    {{ mb_substr($member->name, 0, 1) }}
                </div>
            @endif
            <h4 class="font-black text-xl text-slate-800">{{ $member->name }}</h4>
            <p class="text-gold-600 font-bold mb-2">{{ $member->job_title ?: ($roleLabels[$member->role] ?? 'عضو فريق') }}</p>
            <p class="text-slate-500 text-sm leading-relaxed">{{ $member->team_bio ?: ($roleBios[$member->role] ?? 'خبير في إدارة المنح الدراسية ودعم الطلاب لتحقيق طموحاتهم الأكاديمية.') }}</p>
        </div>
        @endforeach
    </div>
</section>

<section class="py-24 px-8 bg-white">
    <div class="text-center mb-16">
        <h2 data-aos="zoom-in" class="text-4xl font-black text-slate-900 mb-4 text-navy-900">قصص النجاح</h2>
        <p class="text-gray-400 font-medium">طلاب حققوا أحلامهم وانطلقوا نحو العالمية</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        @forelse($testimonials as $index => $testimonial)
        <div data-aos="fade-right" data-aos-delay="{{ $index * 150 }}"
             class="border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl transition-all duration-500 group bg-slate-50/50 hover:bg-white">
            <div class="overflow-hidden rounded-2xl mb-8 shadow-md">
                <img src="{{ $testimonial->avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($testimonial->name) . '&background=6366f1&color=fff&size=400' }}" class="w-full h-52 object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $testimonial->name }}">
            </div>
            <div class="flex text-yellow-400 mb-4 text-sm tracking-widest group-hover:scale-110 transition-transform">
                @for($i = 0; $i < $testimonial->rating; $i++)★@endfor
            </div>
            <p class="text-slate-600 italic mb-8 leading-relaxed">"{{ $testimonial->content }}"</p>
            <div class="flex items-center gap-4 border-t border-slate-100 pt-6">
                <div class="w-14 h-14 bg-gold-600 rounded-2xl flex items-center justify-center font-black text-white shadow-lg shadow-gold-100 group-hover:rotate-12 transition-transform">
                    {{ mb_substr($testimonial->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="font-black text-slate-800 text-lg">{{ $testimonial->name }}</h4>
                    <p class="text-xs text-gold-500 font-bold uppercase tracking-tighter">{{ $testimonial->university }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <span class="text-6xl block mb-6 opacity-30">💬</span>
            <h3 class="text-xl font-black text-slate-500">لا توجد تجارب مشاركة حالياً</h3>
        </div>
        @endforelse
    </div>
</section>

<section class="px-8 pb-24">
    <div data-aos="zoom-in-up" class="max-w-6xl mx-auto bg-gradient-to-br from-navy-900 to-navy-800 rounded-[2.5rem] p-16 text-center text-white relative overflow-hidden shadow-[0_20px_50px_rgba(15,27,61,0.3)]">
        <div class="relative z-10">
            <h2 class="text-5xl font-black mb-6">جاهز لبدء رحلتك؟</h2>
            <p class="opacity-80 mb-12 text-xl max-w-lg mx-auto">انضم إلى آلاف الطلاب الذين حققوا أحلامهم معنا اليوم مجاناً</p>
            <div class="flex justify-center">
                <a href="{{ route('register') }}" class="bg-white text-gold-600 font-black px-16 py-5 rounded-[2rem] hover:bg-slate-100 transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-xl text-lg">
                    ابدأ الآن مجاناً
                </a>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full -mr-48 -mt-48 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-gold-400 opacity-20 rounded-full -ml-36 -mb-36 blur-2xl"></div>
    </div>
</section>

<script>
    // فيديو الروبوت المتحرك بالهيرو: التكرار مفروض بالجافاسكربت بالإضافة لخاصية loop
    // العادية، حتى لو المستخدم عطّل التكرار يدوياً من قائمة الفأرة اليمنى بالمتصفح.
    (function () {
        const video = document.getElementById('orbitMascotVideo');
        if (!video) return;
        video.addEventListener('ended', function () {
            video.currentTime = 0;
            video.play().catch(() => {});
        });
    })();
</script>
@endsection