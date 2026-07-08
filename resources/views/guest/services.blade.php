@extends('layouts.app')

@section('content')
<section class="py-20 px-4 md:px-8 max-w-7xl mx-auto overflow-hidden">
    <div class="text-center mb-24 bg-gradient-to-br from-slate-50 to-indigo-50 rounded-[3rem] p-12 md:p-24 relative overflow-hidden" data-aos="fade-down">
        <div class="relative z-10">
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 mb-8 leading-tight">
                خدماتنا <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-emerald-500">المميزة</span>
            </h1>
            <p class="text-xl text-gray-600 font-bold max-w-3xl mx-auto mb-16 leading-relaxed">
                اختر الخطة المناسبة لك واحصل على دعم شامل من البداية للنهاية في رحلتك الأكاديمية
            </p>
            <div class="flex flex-col sm:flex-row gap-8 justify-center items-center">
                <span class="text-xl md:text-2xl font-black text-emerald-600 flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm">
                    <span class="bg-emerald-100 p-1 rounded-full text-sm">✓</span> مجاني بالكامل حالياً
                </span>
                <span class="text-xl md:text-2xl font-black text-indigo-600 flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm">
                    <span class="bg-indigo-100 p-1 rounded-full text-sm">✨</span> مدعوم بالذكاء الاصطناعي
                </span>
            </div>
        </div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-indigo-200/20 rounded-full blur-3xl"></div>
    <div class="relative mt-20 mb-24">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
            {{-- كارد التقديم المجاني --}}
            <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 p-10 text-center flex flex-col" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
                    🎓
                </div>
                <h2 class="text-2xl font-black text-slate-800 mb-3">التقديم المجاني</h2>
                <p class="text-slate-500 font-bold text-base max-w-sm mx-auto mb-6 leading-relaxed flex-grow">
                    تصفح المنح المتاحة وقدم مباشرة عبر روابط الجامعات الرسمية بدون أي رسوم.
                </p>
                <div class="space-y-3 mb-8 text-right px-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">وصول فوري لجميع المنح</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">روابط مباشرة للجامعات</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-600">بدون قيود أو اشتراكات</span>
                    </div>
                </div>
                <a href="{{ route('guest.scholarships') }}" class="block w-full border-2 border-slate-200 text-slate-700 py-4 rounded-2xl font-black hover:bg-slate-50 transition">
                    استكشف المنح المجانية
                </a>
            </div>

            {{-- كارد التقديم عن طريقنا --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-[3rem] shadow-xl border-2 border-amber-200 p-10 text-center flex flex-col relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 via-yellow-500 to-amber-600"></div>
                <div class="w-20 h-20 bg-gradient-to-br from-amber-400 via-yellow-500 to-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-lg shadow-amber-200">
                    💎
                </div>
                <div class="inline-block bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider mb-3 self-center">خدمة مميزة</div>
                <h2 class="text-2xl font-black text-slate-800 mb-3">التقديم عن طريقنا</h2>
                <p class="text-slate-600 font-bold text-base max-w-sm mx-auto mb-6 leading-relaxed flex-grow">
                    نتولى عنك إجراءات التقديم بالكامل — من مراجعة الأوراق إلى متابعة الطلب حتى القبول.
                </p>
                <div class="space-y-3 mb-8 text-right px-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-700">مراجعة احترافية للأوراق</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-700">متابعة حالة الطلب خطوة بخطوة</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-700">دعم فني شخصي على مدار الساعة</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-bold text-slate-700">بدون أي رسوم خلال الفترة الحالية</span>
                    </div>
                </div>
                <a href="{{ route('guest.scholarships') }}" class="block w-full bg-gradient-to-r from-amber-400 via-yellow-500 to-amber-600 text-white py-4 rounded-2xl font-black shadow-lg shadow-amber-200 hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                    اختر منحتك الآن
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
                    <p class="text-indigo-600 text-sm font-bold">{{ $testimonial->university }}</p>
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

    <div class="text-center py-20 bg-gradient-to-r from-emerald-600 to-green-500 rounded-[4rem] text-white shadow-2xl px-8 relative overflow-hidden" data-aos="zoom-in">
        <div class="relative z-10">
            <h2 class="text-4xl md:text-6xl font-black mb-6 leading-tight text-white">خطوتك الأولى نحو النجاح</h2>
            <p class="text-xl opacity-90 mb-12 max-w-2xl mx-auto">انضم لآلاف الطلاب الناجحين وابدأ رحلة الابتعاث اليوم</p>
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                <input type="email" placeholder="بريدك الإلكتروني" class="w-full md:w-80 px-8 py-5 rounded-2xl text-slate-900 font-bold focus:ring-4 focus:ring-white/30 outline-none text-right">
                <button class="w-full md:w-auto bg-slate-900 text-white px-12 py-5 rounded-2xl font-black text-xl hover:bg-black transition-all">سجل الآن</button>
            </div>
        </div>
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
    </div>
</section>
@endsection