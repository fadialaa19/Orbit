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
    
    <div class="relative" data-aos="zoom-in-right" data-aos-duration="1500">
        <div class="relative z-10 p-10 bg-gradient-to-br from-navy-100 to-white rounded-[2.5rem] shadow-2xl transition-transform duration-700 hover:rotate-2 group">
            <div role="img" aria-label="طالبة تكتب أطروحتها بين الكتب" class="max-w-xs mx-auto transition-all duration-500 group-hover:scale-[1.02]">
                @include('layouts.partials._hero-illustration')
            </div>
        </div>
        
        <div data-aos="fade-down" data-aos-delay="800" class="absolute -top-8 -left-8 bg-white p-5 rounded-3xl shadow-2xl border border-gold-100 flex items-center gap-4 z-20">
            <div class="w-12 h-12 bg-gold-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg">95%</div>
            <div>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider text-right">نسبة التطابق</p>
                <p class="text-sm font-bold text-slate-800">ذكاء اصطناعي</p>
            </div>
        </div>

        <div data-aos="fade-up" data-aos-delay="1000" class="absolute -bottom-8 -right-8 bg-white p-5 rounded-3xl shadow-2xl border border-green-50 flex items-center gap-4 z-20">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-sm font-black text-slate-800">تم القبول!</p>
                <p class="text-[10px] text-gray-400">في جامعة تورنتو</p>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20 relative z-0">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12 px-8">
        @foreach([
            ['+50k', 'طالب مسجل', 'users'],
            ['+2.5k', 'منحة متاحة', 'briefcase'],
            ['+15k', 'قصة نجاح', 'star'],
            ['+120', 'دولة حول العالم', 'globe']
        ] as $index => $stat)
        <div data-aos="fade-up" data-aos-delay="{{ $index * 100 }}" class="text-center group cursor-default">
            <div class="text-gold-600 bg-gold-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gold-600 group-hover:text-white transition-all duration-500 transform group-hover:rotate-12 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-black text-slate-900 mb-2">{{ $stat[0] }}</h2>
            <p class="text-gray-400 font-medium">{{ $stat[1] }}</p>
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

        {{-- عضو ثابت دائم الظهور: مبرمج المنصة، غير مرتبط بحسابات لوحة التحكم --}}
        <div data-aos="fade-up" class="bg-slate-50 p-8 rounded-[2rem] hover:bg-white hover:shadow-xl transition-all duration-500 text-center border-2 border-gold-100">
            <div class="w-20 h-20 bg-gradient-to-br from-sky-500 to-blue-700 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg mx-auto mb-5">
                ف
            </div>
            <h4 class="font-black text-xl text-slate-800">Fadi Hamad</h4>
            <p class="text-gold-600 font-bold mb-2">مطور ومبرمج المنصة</p>
            <p class="text-slate-500 text-sm leading-relaxed">مطور برمجيات Front-End و Back-End، صمّم وبرمج منصة Orbit بالكامل من الفكرة إلى التنفيذ ليقدّم للطلاب تجربة تقنية سلسة وموثوقة.</p>
        </div>

        @foreach($teamMembers as $member)
        <div data-aos="fade-up" class="bg-slate-50 p-8 rounded-[2rem] hover:bg-white hover:shadow-xl transition-all duration-500 text-center">
            <div class="w-20 h-20 bg-gradient-to-br {{ $roleColors[$member->role] ?? 'from-gold-600 to-gold-400' }} rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg mx-auto mb-5">
                {{ mb_substr($member->name, 0, 1) }}
            </div>
            <h4 class="font-black text-xl text-slate-800">{{ $member->name }}</h4>
            <p class="text-gold-600 font-bold mb-2">{{ $roleLabels[$member->role] ?? 'عضو فريق' }}</p>
            <p class="text-slate-500 text-sm leading-relaxed">{{ $roleBios[$member->role] ?? 'خبير في إدارة المنح الدراسية ودعم الطلاب لتحقيق طموحاتهم الأكاديمية.' }}</p>
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
@endsection