@extends('layouts.app')

@section('content')
<section class="py-20 px-4 md:px-8 max-w-7xl mx-auto">
    <!-- Hero -->
    <div class="text-center mb-24" data-aos="fade-down">
        <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-3 rounded-[2rem] text-lg font-black inline-flex items-center gap-3 shadow-xl mb-8 mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            منصة مدعومة بالذكاء الاصطناعي
        </span>
        <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6 leading-tight">بدون قيود <span class="grad-text">تحقق</span> حلمك الدراسي</h1>
        <p class="text-xl text-gray-500 font-bold max-w-3xl mx-auto mb-12 leading-relaxed">نحن هنا لنفتح لك أبواب العالم الأكاديمي. منصة شاملة تجمع بين التكنولوجيا المتقدمة وخبرة الطلاب لضمان نجاحك في الحصول على أفضل المنح الدراسية.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('register') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-12 py-6 rounded-[2.5rem] font-black text-xl shadow-2xl hover:shadow-3xl hover:scale-105 transition-all">ابدأ رحلتك الآن</a>
            <a href="{{ route('guest.scholarships') }}" class="border-3 border-slate-200 text-slate-700 px-12 py-6 rounded-[2.5rem] font-black text-xl hover:bg-slate-50 hover:shadow-xl transition-all">استكشف المنح</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-24 text-center">
        @foreach([
            ['+' . $studentsCount, 'طالب ناجح', '👨‍🎓'],
            ['+' . $scholarshipsCount, 'منحة متاحة', '🏆'],
            ['+' . $universitiesCount, 'جامعة شريكة', '🏫'],
            ['24/7', 'دعم ذكي', '🤖']
        ] as $index => $stat)
        <div class="group p-8" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
            <div class="text-4xl mb-4">{{ $stat[0] }}</div>
            <div class="text-2xl font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $stat[1] }}</div>
            <div class="text-4xl">{{ $stat[2] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Student Journey Infographic -->
    <div class="mb-24">
        <div class="text-center mb-20" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">رحلة الطالب <span class="grad-text">معنا</span></h2>
            <div class="w-24 h-1.5 bg-gradient-to-l from-indigo-600 to-purple-500 mx-auto rounded-full mb-6 shadow-lg"></div>
            <p class="text-xl text-gray-500 font-bold max-w-2xl mx-auto">ثلاث خطوات بسيطة تحول حلمك إلى واقع ملموس</p>
        </div>

        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-1/2 transform -translate-x-1/2 top-32 md:top-48 h-px w-16 md:w-96 bg-gradient-to-r from-indigo-400 to-purple-400 shadow-lg hidden md:block"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-16 items-stretch">
                <!-- Step 1 -->
                <div class="group relative" data-aos="fade-right">
                    <div class="w-28 h-28 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-[3rem] flex flex-col items-center justify-center mx-auto font-black text-2xl shadow-2xl border-8 border-white mb-8 transform group-hover:scale-110 transition-all duration-700 -rotate-12">
                        01
                        <span class="text-sm mt-1 block font-bold">تسجيل</span>
                    </div>
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-slate-50 hover:shadow-2xl hover:-translate-y-6 transition-all duration-500 relative z-10">
                        <h3 class="text-2xl font-black text-slate-800 mb-6 text-center">إنشاء حسابك</h3>
                        <ul class="space-y-4 text-right">
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-700 leading-relaxed">املأ ملفك الشخصي في 3 دقائق</span></li>
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-700 leading-relaxed">اختبار القبول المجاني</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="group relative z-10" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-[3rem] flex flex-col items-center justify-center mx-auto font-black text-2xl shadow-2xl border-8 border-white mb-8 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-700">
                        02
                        <span class="text-sm mt-1 block font-bold">مطابقة</span>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-[2.5rem] p-10 shadow-2xl border-4 border-white hover:shadow-3xl hover:-translate-y-6 transition-all duration-500 relative backdrop-blur-sm">
                        <h3 class="text-2xl font-black text-slate-900 mb-6 text-center grad-text">مطابقة ذكية</h3>
                        <ul class="space-y-4 text-right">
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-800 leading-relaxed">تحليل ملفك بـ AI متقدم</span></li>
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-800 leading-relaxed">عرض أفضل 95% مطابقة</span></li>
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-800 leading-relaxed">تنبيهات فورية للمواعيد</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="group relative" data-aos="fade-left" data-aos-delay="400">
                    <div class="w-28 h-28 bg-gradient-to-br from-emerald-500 to-green-500 text-white rounded-[3rem] flex flex-col items-center justify-center mx-auto font-black text-2xl shadow-2xl border-8 border-white mb-8 transform group-hover:scale-110 transition-all duration-700 rotate-12">
                        03
                        <span class="text-sm mt-1 block font-bold">تقديم</span>
                    </div>
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-slate-50 hover:shadow-2xl hover:-translate-y-6 transition-all duration-500 relative z-10">
                        <h3 class="text-2xl font-black text-slate-800 mb-6 text-center">التقديم والمتابعة</h3>
                        <ul class="space-y-4 text-right">
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-700 leading-relaxed">دليل خطوة بخطوة للتقديم</span></li>
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-700 leading-relaxed">مراجعة الوثائق مجاناً</span></li>
                            <li class="flex items-start gap-4"><svg class="w-7 h-7 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg><span class="font-bold text-slate-700 leading-relaxed">تتبع حالة القبول فوري</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team / Values -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-24">
        <div data-aos="fade-right">
            <h2 class="text-4xl font-black text-slate-900 mb-8">فريقنا</h2>
            <div class="space-y-6">
                @php
                    $roleLabels = [
                        'super_admin' => 'مؤسس ومدير تنفيذي',
                        'scholarship_admin' => 'خبير منح دراسية',
                        'support_admin' => 'مدير دعم فني',
                    ];
                    $roleColors = [
                        'super_admin' => 'from-indigo-500 to-purple-500',
                        'scholarship_admin' => 'from-emerald-500 to-green-500',
                        'support_admin' => 'from-orange-500 to-amber-500',
                    ];
                    $roleBios = [
                        'super_admin' => 'يقود المنصة نحو تمكين آلاف الطلاب من تحقيق أحلامهم الدراسية حول العالم.',
                        'scholarship_admin' => 'خبير في مجال المنح الدولية وشراكات الجامعات العالمية ودعم الطلاب.',
                        'support_admin' => 'مختص في تطوير حلول الدعم الفني وتحسين تجربة المستخدم على المنصة.',
                    ];
                @endphp

                @forelse($teamMembers as $member)
                <div class="flex items-start gap-4 p-6 bg-slate-50 rounded-[2rem] hover:bg-white hover:shadow-lg transition-all">
                    <div class="w-14 h-14 bg-gradient-to-br {{ $roleColors[$member->role] ?? 'from-indigo-500 to-purple-500' }} rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg mt-1 flex-shrink-0">
                        {{ mb_substr($member->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-black text-xl text-slate-800">{{ $member->name }}</h4>
                        <p class="text-indigo-600 font-bold">{{ $roleLabels[$member->role] ?? 'عضو فريق' }}</p>
                        <p class="text-slate-600 mt-2 leading-relaxed">{{ $roleBios[$member->role] ?? 'خبير في إدارة المنح الدراسية ودعم الطلاب لتحقيق طموحاتهم الأكاديمية.' }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <p class="text-slate-400 font-bold">سيتم الإعلان عن الفريق قريباً</p>
                </div>
                @endforelse
            </div>
        </div>
        <div data-aos="fade-left">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Team" class="rounded-[3rem] shadow-2xl w-full h-auto transform hover:scale-105 transition-all duration-500 group-hover:-rotate-1">
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center py-20 bg-gradient-to-br from-indigo-600 via-purple-500 to-pink-500 rounded-[4rem] text-white shadow-2xl px-8" data-aos="zoom-in-up">
        <h2 class="text-4xl md:text-5xl font-black mb-6">هل أنت جاهز؟</h2>
        <p class="text-2xl opacity-90 mb-12 max-w-3xl mx-auto">انضم لآلاف الطلاب الذين غيّروا مستقبلهم معنا</p>
        <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 px-16 py-8 rounded-[3rem] font-black text-2xl shadow-2xl hover:shadow-3xl hover:scale-105 transition-all mx-auto">ابدأ مجاناً اليوم ←</a>
    </div>
</section>
@endsection

