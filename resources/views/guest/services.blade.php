@extends('layouts.app')

@section('content')
<section class="py-20 px-4 md:px-8 max-w-7xl mx-auto overflow-hidden">
    <div class="text-center mb-24 bg-gradient-to-br from-slate-50 to-gold-100 rounded-[2.5rem] p-12 md:p-24 relative overflow-hidden" data-aos="fade-down">
        <div class="relative z-10">
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 mb-8 leading-tight">
                خدماتنا <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-600 to-emerald-500">المميزة</span>
            </h1>
            <p class="text-xl text-gray-600 font-bold max-w-3xl mx-auto mb-16 leading-relaxed">
                اختر الخطة المناسبة لك واحصل على دعم شامل من البداية للنهاية في رحلتك الأكاديمية
            </p>
            <div class="flex flex-col sm:flex-row gap-8 justify-center items-center">
                <span class="text-xl md:text-2xl font-black text-emerald-600 flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm">
                    <span class="bg-emerald-100 p-1 rounded-full text-sm">✓</span> مجاني بالكامل حالياً
                </span>
                <span class="text-xl md:text-2xl font-black text-gold-600 flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm">
                    <span class="bg-gold-100 p-1 rounded-full text-sm">✨</span> مدعوم بالذكاء الاصطناعي
                </span>
            </div>
        </div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-navy-100/20 rounded-full blur-3xl"></div>
    <div class="relative mt-20 mb-24">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
            {{-- كارد الخدمة المجانية --}}
            <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-10 text-center flex flex-col" data-aos="fade-up">
                <div class="w-20 h-20 bg-gradient-to-br from-gold-100 to-navy-100 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
                    🎓
                </div>
                <h2 class="text-2xl font-black text-slate-800 mb-3">التقديم عبر Orbit</h2>
                <p class="text-slate-500 font-bold text-base max-w-sm mx-auto mb-6 leading-relaxed flex-grow">
                    تصفح المنح المتاحة وقدم طلبك مباشرة، مع متابعة حالة طلبك ودعم فني على مدار الساعة.
                </p>
                <div class="space-y-3 mb-8 text-right px-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">وصول فوري لجميع المنح</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">متابعة حالة الطلب خطوة بخطوة</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">دعم فني على مدار الساعة</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">بدون قيود أو اشتراكات</span>
                    </div>
                </div>
                <a href="{{ route('guest.scholarships') }}" class="block w-full bg-gold-600 text-white py-4 rounded-2xl font-black shadow-lg shadow-gold-100 hover:bg-gold-700 transition">
                    استكشف المنح المجانية
                </a>
            </div>

            {{-- كارد خدمة استخراج الأوراق الرسمية --}}
            <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-10 text-center flex flex-col" data-aos="fade-up" data-aos-delay="150">
                <div class="w-20 h-20 bg-gradient-to-br from-navy-100 to-gold-100 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
                    📜
                </div>
                <h2 class="text-2xl font-black text-slate-800 mb-3">استخراج الأوراق الرسمية</h2>
                <p class="text-slate-500 font-bold text-base max-w-sm mx-auto mb-6 leading-relaxed flex-grow">
                    نساعدك تستخرج أوراقك من الوزارات الفلسطينية (شهادة التوجيهي، عدم محكومية، تصديق شهادات وغيرها) نيابة عنك.
                </p>
                <div class="space-y-3 mb-8 text-right px-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">متابعة الإجراءات نيابة عنك</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">مناسبة للطلاب خارج البلاد</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">تغطي أهم المستندات الرسمية</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">تواصل مباشر مع فريقنا</span>
                    </div>
                </div>
                <a href="{{ route('guest.services.documents') }}" class="block w-full bg-navy-900 text-white py-4 rounded-2xl font-black shadow-lg hover:bg-navy-800 transition">
                    اطلع على تفاصيل الخدمة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-32">
        @forelse($testimonials as $index => $testimonial)
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
            <div class="flex items-center gap-4 mb-6">
                <img src="{{ $testimonial->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($testimonial->name) . '&background=6366f1&color=fff' }}" class="w-16 h-16 rounded-2xl object-cover shadow-lg" alt="{{ $testimonial->name }}">
                <div>
                    <h4 class="font-black text-slate-900">{{ $testimonial->name }}</h4>
                    <p class="text-gold-600 text-sm font-bold">{{ $testimonial->university }}</p>
                </div>
            </div>
            <p class="text-slate-600 font-bold leading-relaxed flex-grow italic">"{{ $testimonial->content }}"</p>
            <div class="mt-6 text-amber-400 text-xl">
                @for($i = 0; $i < $testimonial->rating; $i++)★@endfor
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-slate-50 rounded-[3rem]">
            <span class="text-7xl block mb-6">💬</span>
            <h3 class="text-2xl font-black text-slate-800">لا توجد تجارب مشاركة حالياً</h3>
        </div>
        @endforelse
    </div>

    <div class="text-center py-20 bg-gradient-to-r from-navy-900 to-navy-800 rounded-[2.5rem] text-white shadow-2xl px-8 relative overflow-hidden" data-aos="zoom-in">
        <div class="relative z-10">
            <h2 class="text-4xl md:text-6xl font-black mb-6 leading-tight text-white">خطوتك الأولى نحو النجاح</h2>
            <p class="text-xl opacity-90 mb-12 max-w-2xl mx-auto">انضم لآلاف الطلاب الناجحين وابدأ رحلة الابتعاث اليوم</p>
            <div class="flex justify-center">
                <a href="{{ route('register') }}" class="w-full md:w-auto bg-slate-900 text-white px-12 py-5 rounded-2xl font-black text-xl hover:bg-black transition-all">سجل الآن مجاناً</a>
            </div>
        </div>
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
    </div>
</section>
@endsection