@extends('layouts.app')

@section('title', 'استخراج الأوراق الرسمية - Orbit')
@section('meta_description', 'نساعدك تستخرج أوراقك الرسمية من الوزارات الفلسطينية (شهادة التوجيهي، عدم محكومية، تصديق شهادات وغيرها) بدون ما تتعب أو تحتاج تحضر بنفسك.')

@section('content')
<section class="py-20 px-4 md:px-8 max-w-7xl mx-auto">
    <!-- Hero -->
    <div class="text-center mb-20" data-aos="fade-down">
        <span class="bg-gradient-to-r from-navy-900 to-navy-800 text-white px-6 py-3 rounded-[2rem] text-lg font-black inline-flex items-center gap-3 shadow-xl mb-8 mx-auto">
            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            خدمة إضافية من Orbit
        </span>
        <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6 leading-tight">استخراج <span class="grad-text">أوراقك الرسمية</span></h1>
        <p class="text-xl text-gray-500 font-bold max-w-3xl mx-auto leading-relaxed">
            كثير من الطلاب مشغولين بالدراسة أو خارج البلاد وقت التقديم على المنح، وما بيقدروا يروحوا للوزارات بنفسهم.
            فريق Orbit بيتابع استخراج أوراقك الرسمية من الجهات الحكومية الفلسطينية نيابة عنك، وبيوصلك المستند جاهز.
        </p>
    </div>

    @if(!$serviceEnabled)
    <!-- الخدمة متوقفة مؤقتاً -->
    <div class="text-center py-20 bg-slate-50 rounded-[2.5rem] max-w-3xl mx-auto mb-20" data-aos="zoom-in">
        <div class="text-6xl mb-6">🛠️</div>
        <h2 class="text-2xl font-black text-slate-800 mb-4">الخدمة متوقفة مؤقتاً</h2>
        <p class="text-slate-500 font-bold max-w-lg mx-auto leading-relaxed mb-8">
            مندوب استخراج الأوراق الرسمية مش متوفر حالياً، وبنشتغل على تفعيل الخدمة من جديد قريباً. تابعنا أو تواصل معنا لأي استفسار.
        </p>
        <a href="{{ route('guest.contact') }}" class="inline-block bg-gold-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg shadow-gold-100 hover:bg-gold-700 transition">
            تواصل معنا
        </a>
    </div>
    @else
    <!-- المستندات المتاحة -->
    <div class="mb-20">
        <h2 class="text-3xl font-black text-slate-900 text-center mb-4" data-aos="fade-up">المستندات اللي بنساعدك فيها</h2>
        <p class="text-gray-500 font-bold text-center max-w-xl mx-auto mb-14">قائمة بأهم الأوراق الرسمية المطلوبة عادة للتقديم على المنح والسفارات، ومصدرها الرسمي</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($documents as $index => $doc)
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl border border-slate-50 hover:-translate-y-2 hover:border-gold-100 transition-all duration-500" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="w-16 h-16 bg-gradient-to-br from-gold-100 to-cream-50 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner">
                    {{ $doc->icon }}
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-2 leading-snug">{{ $doc->title }}</h3>
                <span class="inline-block bg-navy-100 text-navy-700 px-3 py-1 rounded-lg text-[11px] font-black mb-4">{{ $doc->source }}</span>
                <p class="text-sm text-slate-500 font-bold leading-relaxed">{{ $doc->description }}</p>
            </div>
            @endforeach
        </div>

        <p class="text-center text-slate-400 font-bold text-sm mt-10">
            محتاج مستند رسمي تاني مو موجود بالقائمة؟ تواصل معنا وبنشوف كيف نقدر نساعدك.
        </p>
    </div>

    <!-- كيف تعمل الخدمة -->
    <div class="bg-gradient-to-br from-slate-50 to-gold-50/50 rounded-[2.5rem] p-10 md:p-16 mb-20" data-aos="zoom-in">
        <h2 class="text-3xl font-black text-slate-900 text-center mb-14">كيف تعمل الخدمة؟</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @php
                $steps = [
                    ['num' => '1', 'title' => 'تواصل معنا', 'desc' => 'حدد المستند المطلوب استخراجه وأرسل بياناتك الأساسية عبر صفحة التواصل.'],
                    ['num' => '2', 'title' => 'نتابع الإجراءات', 'desc' => 'فريقنا بيتابع الإجراءات مباشرة مع الجهة الحكومية المختصة نيابة عنك.'],
                    ['num' => '3', 'title' => 'يوصلك المستند', 'desc' => 'بمجرد ما يكون المستند جاهز، بنوصلك إياه أو نرشدك لطريقة استلامه.'],
                ];
            @endphp
            @foreach($steps as $step)
            <div class="text-center">
                <div class="w-16 h-16 bg-gold-600 text-white rounded-2xl flex items-center justify-center text-2xl font-black mx-auto mb-6 shadow-lg shadow-gold-100">
                    {{ $step['num'] }}
                </div>
                <h3 class="font-black text-slate-800 text-lg mb-3">{{ $step['title'] }}</h3>
                <p class="text-sm text-slate-500 font-bold leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center py-20 bg-gradient-to-r from-navy-900 to-navy-800 rounded-[2.5rem] text-white shadow-2xl px-8 relative overflow-hidden" data-aos="zoom-in-up">
        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-black mb-6">جاهز تبدأ؟</h2>
            <p class="text-lg opacity-90 mb-10 max-w-2xl mx-auto">تواصل معنا وحدد المستند اللي محتاجه، وخلي فريق Orbit يهتم بالباقي.</p>
            <div class="flex justify-center">
                <a href="{{ route('guest.contact') }}" class="bg-gold-600 text-white px-12 py-5 rounded-[2.5rem] font-black text-xl shadow-2xl hover:bg-gold-700 hover:scale-105 transition-all whitespace-nowrap">
                    تواصل معنا الآن ←
                </a>
            </div>
        </div>
    </div>
    @endif
</section>
@endsection
