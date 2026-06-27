@extends('layouts.dashboard')

@section('title', 'الخطط والأسعار')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-12 px-4 md:px-10" dir="rtl">
    <div class="max-w-6xl mx-auto">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl font-black text-indigo-600 mb-4">الخطط والأسعار</h1>
            <p class="text-slate-500 font-bold italic">اختر الباقة المناسبة لتحقيق أحلامك الدراسية</p>
            
            <div class="flex items-center justify-center gap-4 mt-8" x-data="{ annual: false }">
                <span class="text-sm font-black text-slate-400">شهري</span>
                <button @click="annual = !annual" class="w-14 h-7 bg-slate-200 rounded-full relative p-1 transition-colors" :class="annual ? 'bg-indigo-600' : 'bg-slate-200'">
                    <div class="w-5 h-5 bg-white rounded-full shadow-sm transform transition-transform" :class="annual ? '-translate-x-7' : 'translate-x-0'"></div>
                </button>
                <span class="text-sm font-black text-indigo-600">سنوي <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-lg text-[10px]">وفر 20%</span></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
            
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-50 flex flex-col items-center hover:shadow-xl transition-shadow relative overflow-hidden group">
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">⭐</div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">أساسي</h3>
                <p class="text-slate-400 text-xs font-bold mb-6">للطلاب الذين يبدأون رحلتهم</p>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-5xl font-black text-slate-800">0</span>
                    <span class="text-slate-400 font-bold">₪ / شهرياً</span>
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

            <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-indigo-100 border-2 border-indigo-500 flex flex-col items-center relative transform md:-translate-y-4">
                <div class="absolute top-6 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[10px] font-black px-4 py-1 rounded-full uppercase tracking-widest">الأكثر طلباً</div>
                <div class="w-16 h-16 bg-indigo-50 rounded-3xl flex items-center justify-center text-3xl mb-6 mt-4">🚀</div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">احترافي</h3>
                <p class="text-slate-400 text-xs font-bold mb-6">الأفضل للطلاب الجادين</p>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-5xl font-black text-indigo-600">599</span>
                    <span class="text-slate-400 font-bold">₪ / سنوياً</span>
                </div>
                <ul class="space-y-4 w-full mb-10">
                    @foreach(['جميع مميزات الخطة الأساسية', 'توصيات ذكاء اصطناعي', 'مراجعة رسالة الدوافع', 'استشارة فورية مع خبراء'] as $feature)
                    <li class="flex items-center justify-end gap-3 text-sm font-bold text-slate-600">
                        <span>{{ $feature }}</span>
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </li>
                    @endforeach
                </ul>
                <button class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 mt-auto">اشترك الآن</button>
            </div>

            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-50 flex flex-col items-center hover:shadow-xl transition-shadow group">
                <div class="w-16 h-16 bg-purple-50 rounded-3xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">💎</div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">بريميوم</h3>
                <p class="text-slate-400 text-xs font-bold mb-6">دعم كامل من البداية للنهاية</p>
                <div class="flex items-baseline gap-1 mb-8">
                    <span class="text-5xl font-black text-slate-800">1499</span>
                    <span class="text-slate-400 font-bold">₪ / سنة</span>
                </div>
                <ul class="space-y-4 w-full mb-10">
                    @foreach(['مرافقة كاملة في عملية التقديم', 'تجهيز ملف السيرة الذاتية', 'تدريبات مقابلة شخصية', 'ضمان استرداد الأموال'] as $feature)
                    <li class="flex items-center justify-end gap-3 text-sm font-bold text-slate-600">
                        <span>{{ $feature }}</span>
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </li>
                    @endforeach
                </ul>
                <button class="w-full py-4 bg-slate-800 text-white rounded-2xl font-black hover:bg-slate-900 transition mt-auto">تواصل معنا</button>
            </div>
        </div>

        <h2 class="text-2xl font-black text-slate-800 text-center mb-10">خدمات إضافية منفصلة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-20">
            @foreach([
                ['مراجعة رسالة التحفيز', '199', 'تحسين وتنسيق الخطاب الشخصي'],
                ['تحسين السيرة الذاتية', '149', 'كتابة CV احترافي بنظام ATS'],
                ['استشارة فردية', '299', 'جلسة 45 دقيقة مع خبير منح'],
                ['تدريب على المقابلة', '399', 'تجهيزك لأهم أسئلة لجان القبول']
            ] as $service)
            <div class="bg-white p-6 rounded-3xl border border-slate-50 flex items-center justify-between hover:bg-indigo-50/30 transition group">
                <button class="bg-white border-2 border-slate-100 text-slate-500 px-6 py-2 rounded-xl text-xs font-black group-hover:border-indigo-600 group-hover:text-indigo-600 transition">طلب الخدمة</button>
                <div class="text-right">
                    <div class="flex items-center justify-end gap-2">
                        <span class="text-xl font-black text-slate-800">{{ $service[1] }} ₪</span>
                        <h4 class="font-black text-slate-700">{{ $service[0] }}</h4>
                    </div>
                    <p class="text-slate-400 text-[10px] font-bold">{{ $service[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="bg-indigo-600 rounded-[3rem] p-10 text-center text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-black mb-4">لديك أسئلة؟</h3>
                <p class="text-indigo-100 font-bold mb-8">نحن هنا لمساعدتك في اختيار الباقة المناسبة لمستقبلك</p>
                <div class="flex flex-col md:flex-row justify-center gap-12">
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-2xl">🛡️</span>
                        <span class="font-black">ضمان استرداد المال</span>
                        <p class="text-[10px] text-indigo-200">خلال 14 يوم من الاشتراك</p>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-2xl">🎧</span>
                        <span class="font-black">دعم فني متميز</span>
                        <p class="text-[10px] text-indigo-200">متاح على مدار الساعة</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection