@extends('layouts.app')

@section('content')
<style>
    .match-circle {
        --progress: 95;
        --size: 5rem;
    }
    .match-circle svg circle {
        stroke-dasharray: calc(var(--progress) * 1.4), 314;
    }
</style>

<section class="py-20 px-4 md:px-8 max-w-7xl mx-auto">
    <!-- Header & Search -->
    <div class="text-center mb-20" data-aos="fade-down">
        <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6">اكتشف منحك <span class="grad-text">المثالية</span></h1>
        <p class="text-xl text-gray-500 font-bold max-w-2xl mx-auto mb-12">ابحث بين {{ $activeCount }}+ منحة دراسية حول العالم باستخدام فلتر البحث المتقدم المدعوم بالذكاء الاصطناعي</p>
        <div class="stats-grid grid grid-cols-2 md:grid-cols-4 gap-8 mb-16 max-w-4xl mx-auto">
            @foreach([
                ['+' . $activeCount, 'منحة متاحة', '🎓'],
                ['120+', 'دولة', '🌍'],
                ['95%', 'متوسط مطابقة', '🤖'],
                ['100%', 'مجاني التسجيل', '✅']
            ] as $stat)
            <div class="group" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="text-3xl md:text-4xl mb-2">{{ $stat[0] }}</div>
                <div class="text-slate-600 font-bold text-sm group-hover:text-indigo-600">{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-[3rem] shadow-xl p-6 md:p-8 mb-16 max-w-4xl mx-auto border border-slate-50" data-aos="zoom-in">
        <form method="GET" action="{{ route('guest.scholarships') }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-end">
                <div class="relative">
                    <svg class="w-6 h-6 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="ابحث عن منحة، جامعة، تخصص..." class="w-full pl-12 pr-6 py-4 rounded-[2.5rem] bg-slate-50 border-2 border-slate-100 focus:border-indigo-300 text-right font-bold text-lg focus:outline-none focus:bg-white transition-all">
                </div>
                <select name="category" class="p-4 rounded-[2.5rem] bg-slate-50 border-2 border-slate-100 focus:border-indigo-300 font-bold text-right text-lg focus:outline-none">
                    <option value="">جميع المستويات</option>
                    <option value="بكالوريوس" {{ ($category ?? '') == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                    <option value="ماجستير" {{ ($category ?? '') == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                    <option value="دكتوراه" {{ ($category ?? '') == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                </select>
                <div class="grid grid-cols-2 gap-3 lg:col-span-1">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-4 px-6 rounded-[2rem] font-black shadow-lg hover:shadow-xl transition-all text-lg">بحث متقدم</button>
                    <a href="{{ route('register') }}" class="border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white py-4 px-6 rounded-[2rem] font-black shadow-lg hover:shadow-xl transition-all text-lg text-center flex items-center justify-center">ابدأ مجاناً</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Scholarships Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-20">
        @forelse($scholarships as $scholarship)
        @php
            $matchPercent = 80 + ($scholarship->id % 19);
            $coverageList = $scholarship->coverage ?? [];
            $fundingType = (is_array($coverageList) && count($coverageList) >= 2) ? 'ممولة كاملة' : 'جزئياً ممونة';
            $coverageDisplay = is_array($coverageList) && !empty($coverageList) ? $coverageList[0] : 'ممونة';
            $countryCode = strtoupper(mb_substr($scholarship->country, 0, 2));
        @endphp
        <div class="group bg-white rounded-[2.5rem] p-8 shadow-sm hover:shadow-2xl border border-slate-50 hover:-translate-y-4 hover:border-indigo-200 transition-all duration-500 overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <!-- Match Score Badge -->
            <div class="absolute top-6 right-6 w-20 h-20">
                <svg class="w-full h-full -rotate-12" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="35" fill="none" stroke="#f8fafc" stroke-width="4"></circle>
                    <circle cx="40" cy="40" r="35" fill="none" stroke="#4f46e5" stroke-width="4" stroke-linecap="round" stroke-dasharray="{{ $matchPercent * 1.11 }}, 220"></circle>
                    <text x="40" y="45" font-size="16" font-weight="bold" fill="#1e293b" text-anchor="middle" dy=".3em">{{ $matchPercent }}%</text>
                </svg>
            </div>

            <!-- University Logo -->
            <div class="w-20 h-20 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-[1.5rem] flex items-center justify-center mb-6 relative z-10 shadow-inner border-4 border-white">
                <span class="text-2xl font-black text-indigo-600 drop-shadow-lg">{{ $countryCode }}</span>
            </div>

            <!-- Content -->
            <div class="relative z-10">
                <h3 class="text-xl font-black text-slate-800 mb-3 leading-tight">{{ $scholarship->title_ar }}</h3>
                <div class="flex items-center gap-2 mb-6 opacity-80">
                    <span class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center text-indigo-600 font-bold text-sm">{{ $countryCode }}</span>
                    <span class="text-lg font-bold text-slate-700">{{ $scholarship->university }}</span>
                </div>

                <!-- Badges -->
                <div class="grid grid-cols-3 gap-2 mb-8">
                    <span class="bg-green-50 text-green-600 px-3 py-2 rounded-xl text-xs font-black text-center shadow-sm">💰 {{ $coverageDisplay }}</span>
                    <span class="bg-indigo-50 text-indigo-600 px-3 py-2 rounded-xl text-xs font-black text-center shadow-sm">🎓 {{ $scholarship->category }}</span>
                    <span class="bg-purple-50 text-purple-600 px-3 py-2 rounded-xl text-xs font-black text-center shadow-sm">📅 {{ $scholarship->formatted_deadline }}</span>
                </div>

                <!-- Funding Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-3xl font-black text-sm mb-8 shadow-md
                    {{ $fundingType == 'ممولة كاملة' ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-indigo-200' : 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-emerald-200' }}">
                    {{ $fundingType === 'ممولة كاملة' ? '🌟 ' : '⭐ ' }}{{ $fundingType }}
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('login') }}" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 px-8 rounded-3xl font-black shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all text-lg group-hover:bg-indigo-700 text-center block">عرض التفاصيل</a>
                    <a href="{{ route('login') }}" class="flex-1 border-2 border-slate-200 text-slate-700 py-4 px-6 rounded-3xl font-black hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        حفظ
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-20">
            <div class="text-6xl mb-6">🔍</div>
            <h3 class="text-2xl font-black text-slate-800 mb-4">لا توجد منح متاحة</h3>
            <p class="text-slate-500 font-bold">حاول تعديل معايير البحث أو عد لاحقاً</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($scholarships->hasPages())
    <div class="mb-20 flex justify-center">
        {{ $scholarships->links() }}
    </div>
    @endif

    <!-- CTA Section -->
    <div class="text-center py-20 bg-gradient-to-b from-indigo-600 to-purple-500 rounded-[4rem] text-white shadow-2xl max-w-4xl mx-auto px-8" data-aos="zoom-in-up">
        <h2 class="text-4xl font-black mb-6">وجدت منحتك؟</h2>
        <p class="text-xl opacity-90 mb-12 max-w-2xl mx-auto">سجل مجاناً لمتابعة التقديم واحصل على تحليل مخصص لملفك الشخصي</p>
        <div class="flex flex-col md:flex-row gap-6 justify-center items-center">
            <input type="email" placeholder="بريدك الإلكتروني" class="flex-1 max-w-md px-8 py-5 rounded-[2.5rem] text-slate-800 font-bold text-lg shadow-inner focus:outline-none focus:ring-4 focus:ring-white/30">
            <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-12 py-5 rounded-[2.5rem] font-black text-xl shadow-2xl hover:shadow-3xl hover:scale-105 transition-all whitespace-nowrap">ابدأ مجاناً ←</a>
        </div>
    </div>
</section>
@endsection

