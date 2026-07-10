@extends('layouts.dashboard')

@section('title', 'خدماتنا')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-12 px-4 md:px-10" dir="rtl">
    <div class="max-w-6xl mx-auto">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl font-black text-gold-600 mb-4">خدماتنا</h1>
            <p class="text-slate-500 font-bold italic">جميع الخدمات متاحة مجاناً بالكامل خلال الفترة الحالية</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
            
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-50 flex flex-col items-center hover:shadow-xl transition-shadow relative overflow-hidden group">
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">⭐</div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">أساسي</h3>
                <p class="text-slate-400 text-xs font-bold mb-6">للطلاب الذين يبدأون رحلتهم</p>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-black text-emerald-600">مجاناً</span>
                </div>
                <ul class="space-y-4 w-full mb-10">
                    @foreach(['الوصول إلى 50 منحة', 'تنبيهات أساسية', 'محرك بحث متطور'] as $feature)
                    <li class="flex items-center justify-end gap-3 text-sm font-bold text-slate-600">
                        <span>{{ $feature }}</span>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </li>
                    @endforeach
                </ul>
                <button class="w-full py-4 bg-slate-800 text-white rounded-2xl font-black hover:bg-slate-900 transition mt-auto">ابدأ مجاناً</button>
            </div>

            <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gold-100 border-2 border-gold-500 flex flex-col items-center relative transform md:-translate-y-4">
                <div class="absolute top-6 left-1/2 -translate-x-1/2 bg-gold-600 text-white text-[10px] font-black px-4 py-1 rounded-full uppercase tracking-widest">الأكثر طلباً</div>
                <div class="w-16 h-16 bg-gold-100 rounded-3xl flex items-center justify-center text-3xl mb-6 mt-4">🚀</div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">احترافي</h3>
                <p class="text-slate-400 text-xs font-bold mb-6">الأفضل للطلاب الجادين</p>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-black text-emerald-600">مجاناً</span>
                </div>
                <ul class="space-y-4 w-full mb-10">
                    @foreach(['جميع مميزات الخطة الأساسية', 'توصيات ذكاء اصطناعي', 'مراجعة رسالة الدوافع', 'استشارة فورية مع خبراء'] as $feature)
                    <li class="flex items-center justify-end gap-3 text-sm font-bold text-slate-600">
                        <span>{{ $feature }}</span>
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </li>
                    @endforeach
                </ul>
                <button class="w-full py-4 bg-gold-600 text-white rounded-2xl font-black hover:bg-gold-700 transition shadow-lg shadow-navy-100 mt-auto">ابدأ الآن مجاناً</button>

            </div>

            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-50 flex flex-col items-center hover:shadow-xl transition-shadow group">
                <div class="w-16 h-16 bg-gold-100 rounded-3xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">💎</div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">بريميوم</h3>
                <p class="text-slate-400 text-xs font-bold mb-6">دعم كامل من البداية للنهاية</p>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-4xl font-black text-emerald-600">مجاناً</span>
                </div>
                <ul class="space-y-4 w-full mb-10">
                    @foreach(['مرافقة كاملة في عملية التقديم', 'تجهيز ملف السيرة الذاتية', 'تدريبات مقابلة شخصية', 'دعم أولوية على مدار الساعة'] as $feature)
                    <li class="flex items-center justify-end gap-3 text-sm font-bold text-slate-600">
                        <span>{{ $feature }}</span>
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </li>
                    @endforeach
                </ul>
                <button class="w-full py-4 bg-slate-800 text-white rounded-2xl font-black hover:bg-slate-900 transition mt-auto">تواصل معنا</button>
            </div>
        </div>

        <h2 class="text-2xl font-black text-slate-800 text-center mb-10">خدمات إضافية منفصلة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-20">
            @foreach([
                ['مراجعة رسالة التحفيز', 'تحسين وتنسيق الخطاب الشخصي'],
                ['تحسين السيرة الذاتية', 'كتابة CV احترافي بنظام ATS'],
                ['استشارة فردية', 'جلسة 45 دقيقة مع خبير منح'],
                ['تدريب على المقابلة', 'تجهيزك لأهم أسئلة لجان القبول']
            ] as $service)
            <div class="bg-white p-6 rounded-3xl border border-slate-50 flex items-center justify-between hover:bg-gold-100/30 transition group">
                <button class="bg-white border-2 border-slate-100 text-slate-500 px-6 py-2 rounded-xl text-xs font-black group-hover:border-gold-600 group-hover:text-gold-600 transition">طلب الخدمة</button>
                <div class="text-right">
                    <div class="flex items-center justify-end gap-2">
                        <span class="text-xl font-black text-emerald-600">مجاناً</span>
                        <h4 class="font-black text-slate-700">{{ $service[0] }}</h4>
                    </div>
                    <p class="text-slate-400 text-[10px] font-bold">{{ $service[1] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="bg-gold-600 rounded-[3rem] p-10 text-center text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-black mb-4">لديك أسئلة؟</h3>
                <p class="text-gold-100 font-bold mb-8">نحن هنا لمساعدتك، جميع خدماتنا متاحة مجاناً حالياً</p>
                <div class="flex flex-col md:flex-row justify-center gap-12">
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-2xl">🎉</span>
                        <span class="font-black">وصول مجاني كامل</span>
                        <p class="text-[10px] text-navy-100">لفترة محدودة</p>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-2xl">🎧</span>
                        <span class="font-black">دعم فني متميز</span>
                        <p class="text-[10px] text-navy-100">متاح على مدار الساعة</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection