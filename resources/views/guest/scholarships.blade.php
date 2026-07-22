@extends('layouts.app')

@section('content')

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
                <div class="text-slate-600 font-bold text-sm group-hover:text-gold-600">{{ $stat[1] }}</div>
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
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="ابحث عن منحة، جامعة، تخصص..." class="w-full pl-12 pr-6 py-4 rounded-[2.5rem] bg-slate-50 border-2 border-slate-100 focus:border-gold-300 text-right font-bold text-lg focus:outline-none focus:bg-white transition-all">
                </div>
                <select name="category" class="p-4 rounded-[2.5rem] bg-slate-50 border-2 border-slate-100 focus:border-gold-300 font-bold text-right text-lg focus:outline-none">
                    <option value="">جميع المستويات</option>
                    <option value="Bachelor" {{ ($category ?? '') == 'Bachelor' ? 'selected' : '' }}>بكالوريوس</option>
                    <option value="Master" {{ ($category ?? '') == 'Master' ? 'selected' : '' }}>ماجستير</option>
                    <option value="PhD" {{ ($category ?? '') == 'PhD' ? 'selected' : '' }}>دكتوراه</option>
                    <option value="Short Course" {{ ($category ?? '') == 'Short Course' ? 'selected' : '' }}>كورس قصير</option>
                </select>
                <div class="grid grid-cols-2 gap-3 lg:col-span-1">
                    <button type="submit" class="bg-gold-600 hover:bg-gold-700 text-white py-4 px-6 rounded-[2rem] font-black shadow-lg hover:shadow-xl transition-all text-lg">بحث متقدم</button>
                    <a href="{{ route('register') }}" class="border-2 border-gold-600 text-gold-600 hover:bg-gold-600 hover:text-white py-4 px-6 rounded-[2rem] font-black shadow-lg hover:shadow-xl transition-all text-lg text-center flex items-center justify-center">ابدأ مجاناً</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Scholarships List -->
    <div class="space-y-6 mb-20">
        @forelse($scholarships as $scholarship)
        @php
            $coverageList = $scholarship->coverage ?? [];
            $fundingType = (is_array($coverageList) && count($coverageList) >= 2) ? 'ممولة كاملة' : 'جزئياً ممونة';
            $coverageDisplay = is_array($coverageList) && !empty($coverageList) ? $coverageList[0] : 'ممونة';
            $countryCode = strtoupper(mb_substr($scholarship->country, 0, 2));
        @endphp
        <div class="group bg-white rounded-[2.5rem] shadow-sm hover:shadow-xl border border-slate-50 hover:border-navy-100 transition-all duration-500 overflow-hidden relative" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            {{-- رابط يغطي الكارد بالكامل حتى الضغط بأي مكان ينقل لصفحة تفاصيل المنحة --}}
            <a href="{{ route('guest.scholarships.show', $scholarship->id) }}" class="absolute inset-0 z-10" aria-label="{{ $scholarship->title_ar }}"></a>

            @if($scholarship->main_image)
                <div class="w-full aspect-[2/1] md:aspect-[5/1] overflow-hidden relative bg-gradient-to-br from-slate-100 to-slate-50">
                    <img src="{{ $scholarship->main_image }}" alt="" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-8">
                <div class="flex flex-col md:flex-row gap-8 items-center">

                    <!-- University Logo -->
                    <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 bg-gradient-to-br from-gold-100 to-cream-50 rounded-[1.5rem] flex items-center justify-center relative z-10 pointer-events-none shadow-inner border-4 border-white overflow-hidden {{ $scholarship->main_image ? 'shadow-lg' : '' }}">
                        @if($scholarship->logo_image)
                            <img src="{{ $scholarship->logo_image }}" alt="{{ $scholarship->title_ar }}" class="w-full h-full object-contain p-2">
                        @else
                            <span class="text-2xl font-black text-gold-600 drop-shadow-lg">{{ $countryCode }}</span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 text-center md:text-right">
                        <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-[10px] font-black">🌍 {{ $scholarship->country }}</span>
                            <h3 class="text-xl font-black text-slate-800">{{ $scholarship->title_ar }}</h3>
                        </div>
                        <p class="text-sm text-slate-400 font-bold mb-4">{{ $scholarship->university }}</p>

                        <div class="flex flex-wrap justify-center md:justify-start gap-3">
                            <span class="bg-green-50 text-green-600 px-3 py-1.5 rounded-xl text-xs font-black">💰 {{ $coverageDisplay }}</span>
                            <span class="bg-gold-100 text-gold-600 px-3 py-1.5 rounded-xl text-xs font-black">🎓 {{ $scholarship->category_label }}</span>
                            <span class="bg-slate-50 text-slate-500 px-3 py-1.5 rounded-xl text-xs font-black">📅 {{ $scholarship->formatted_deadline }}</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="relative z-20 flex flex-col gap-3 w-full md:w-auto items-center md:items-end shrink-0">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-3xl font-black text-xs shadow-md
                            {{ $fundingType == 'ممولة كاملة' ? 'bg-gradient-to-r from-gold-600 to-gold-400 text-white shadow-gold-100' : 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-emerald-200' }}">
                            {{ $fundingType === 'ممولة كاملة' ? '🌟 ' : '⭐ ' }}{{ $fundingType }}
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <a href="{{ route('guest.scholarships.show', $scholarship->id) }}" class="bg-gold-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-gold-100 hover:bg-gold-700 transition text-center block w-full md:w-auto">
                                عرض التفاصيل
                            </a>
                            <a href="{{ route('login') }}" class="bg-slate-50 hover:bg-red-50 hover:text-red-500 text-slate-400 p-3 rounded-xl transition flex items-center justify-center shrink-0" title="حفظ">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 bg-white rounded-[2.5rem] border border-slate-50">
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
    <div class="text-center py-20 bg-gradient-to-b from-navy-900 to-navy-800 rounded-[2.5rem] text-white shadow-2xl max-w-4xl mx-auto px-8" data-aos="zoom-in-up">
        <h2 class="text-4xl font-black mb-6">وجدت منحتك؟</h2>
        <p class="text-xl opacity-90 mb-12 max-w-2xl mx-auto">سجل مجاناً لمتابعة التقديم واحصل على تحليل مخصص لملفك الشخصي</p>
        <div class="flex justify-center">
            <a href="{{ route('register') }}" class="bg-gold-600 text-white px-12 py-5 rounded-[2.5rem] font-black text-xl shadow-2xl hover:bg-gold-700 hover:scale-105 transition-all whitespace-nowrap">ابدأ مجاناً ←</a>
        </div>
    </div>
</section>
@endsection

