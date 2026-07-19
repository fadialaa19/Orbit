@extends('layouts.dashboard')

@section('title', 'تفاصيل المنحة')

@section('header_search', '')

@section('content')
<style>.prose img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 1rem 0; }</style>
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10">
    <div class="max-w-5xl mx-auto">
        
        {{-- زر العودة للمنح السابقة --}}
        <div class="flex justify-end mb-6">
            <a href="{{ route('dashboard.scholarships') }}" class="flex items-center gap-2 text-slate-500 hover:text-gold-600 font-bold transition">
                <span>العودة للمنح</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        {{-- حاوية الكرت الرئيسي للمنحة شاملة الكفر والتفاصيل --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
            
            {{-- 1. كفر المنحة الفعلي من قاعدة البيانات --}}
            <div class="relative w-full aspect-[5/1] bg-slate-100">
                @if($scholarship->main_image)
                    <img src="{{ $scholarship->main_image }}" alt="{{ $scholarship->title_ar }}" class="w-full h-full object-cover">
                @else
                    {{-- fallback الافتراضي --}}
                    <div class="w-full h-full bg-gradient-to-r from-gold-500 via-gold-500 to-pink-500"></div>
                @endif
                <div class="absolute inset-0 bg-black/10"></div>
            </div>

            {{-- 2. تفاصيل المنحة وجسم الكرت العلوي --}}
            <div class="p-8 relative">
                <div class="flex flex-col md:flex-row gap-10 items-start">
                    
                    {{-- القسم الجانبي: لوجو المنحة المجلوب من الداتابيز وأزرار التفاعل السريع --}}
                    <div class="w-full md:w-48 flex flex-col items-center gap-6">
                        
                        {{-- 2. لوجو المنحة الفعلي المربوط بـ logo_image كـ URL كامل --}}
                        <div class="w-32 h-32 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center p-3 shadow-inner overflow-hidden">
                            @if($scholarship->logo_image)
                                <img src="{{ $scholarship->logo_image }}" alt="لوجو {{ $scholarship->title_ar }}" class="w-full h-full object-contain">
                            @else
                                <div class="w-full h-full bg-gold-100 rounded-xl flex items-center justify-center text-gold-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174L11.25 15.89c.445.365 1.055.365 1.5 0l6.99-5.717m-.003 4.31v4.454a2.25 2.25 0 01-2.247 2.247H6.75a2.25 2.25 0 01-2.247-2.247v-4.454m15.122-4.31L12 3l-8.12 6.634m16.24 0l-1.92 11.52H5.8l-1.92-11.52z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- أزرار التحكم والتقديم السريع --}}
                        <div class="w-full space-y-3">
                            @if($scholarship->application_url)
                                <a href="{{ $scholarship->application_url }}" target="_blank" rel="noopener noreferrer"
                                   class="w-full block bg-gold-600 text-white py-3.5 rounded-2xl font-black text-sm text-center shadow-lg shadow-gold-100 hover:bg-gold-700 hover:scale-[1.01] transition-all duration-200 flex items-center justify-center gap-2">
                                    <span>قدم الآن مجاناً</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            @endif

                            @if($scholarship->apply_via_us_link)
                                <a href="{{ $scholarship->apply_via_us_link }}" target="_blank" rel="noopener noreferrer"
                                   class="w-full block bg-navy-900 text-white py-3.5 rounded-2xl font-black text-sm text-center shadow-lg hover:bg-navy-800 hover:scale-[1.01] transition-all duration-200 flex items-center justify-center gap-2">
                                    <span>التقديم عن طريقنا</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8m0 0l-3-3m3 3l-3 3M3 12a9 9 0 1018 0 9 9 0 00-18 0z"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- زر حفظ المنحة في المفضلة --}}
                            @php
                                $isFavorited = auth()->check() && $scholarship->favoritedBy()->where('user_id', auth()->id())->exists();
                            @endphp
                            
                            <form action="{{ route('dashboard.scholarships.favorite', $scholarship->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 border py-3 rounded-2xl font-black text-sm transition {{ $isFavorited ? 'bg-red-50 border-red-200 text-red-500' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                                    <svg class="w-4 h-4" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span>{{ $isFavorited ? 'إزالة من المفضلة' : 'حفظ المنحة' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- القسم الأساسي: المسميات والبيانات المفصلة للمنحة من الداتابيز --}}
                    <div class="flex-1 text-right w-full">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-end gap-3 mb-2">
                            <span class="bg-gold-100 text-gold-600 px-3 py-1 rounded-lg text-xs font-black self-start sm:self-auto order-2 sm:order-1">
                                {{ $scholarship->category_label ?: 'منحة دراسية' }}
                            </span>
                            <h1 class="text-2xl md:text-3xl font-black text-slate-800 order-1 sm:order-2">{{ $scholarship->title_ar }}</h1>
                        </div>
                        <p class="text-slate-400 font-bold mb-8 text-base">{{ $scholarship->university }}</p>

                        {{-- شبكة المعلومات الأربعة الأساسية --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                            <div class="bg-slate-50 p-4 rounded-2xl flex items-center justify-end gap-4 border border-slate-100/50">
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">الدولة</p>
                                    <p class="text-sm font-black text-slate-700">{{ $scholarship->country }}</p>
                                </div>
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-lg">📍</div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl flex items-center justify-end gap-4 border border-slate-100/50">
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">آخر موعد للتقديم</p>
                                    <p class="text-sm font-black text-slate-700">{{ $scholarship->formatted_deadline ?? $scholarship->deadline }}</p>
                                </div>
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-gold-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl flex items-center justify-end gap-4 border border-slate-100/50">
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">نوع التغطية المالية</p>
                                    <p class="text-sm font-black text-emerald-600">
                                        @if(is_array($scholarship->coverage))
                                            {{ implode(' ، ', $scholarship->coverage) }}
                                        @else
                                            {{ $scholarship->financial_value ?? 'تمويل كامل' }}
                                        @endif
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-lg">💰</div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl flex items-center justify-end gap-4 border border-slate-100/50">
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">المرحلة الدراسية</p>
                                    <p class="text-sm font-black text-slate-700">
                                        {{ $scholarship->category_label ?: 'كل المراحل' }}
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-gold-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- أوسمة وتصنيفات التوصية الذكية السفلية --}}
                        <div class="flex flex-wrap justify-end gap-3">
                            @foreach($scholarship->recommended_tags ?? [] as $tag)
                                <span class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded-full text-xs font-black">🔥 {{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- تحليل التوافق الذكي --}}
        @if($matchScore)
        @php
            $score = $matchScore->score;
            $scoreColor = $score >= 70 ? 'text-emerald-600' : ($score >= 40 ? 'text-amber-600' : 'text-rose-600');
            $scoreBg = $score >= 70 ? 'bg-emerald-50 border-emerald-100' : ($score >= 40 ? 'bg-amber-50 border-amber-100' : 'bg-rose-50 border-rose-100');
        @endphp
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-10 mb-8">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="w-28 h-28 rounded-full border-4 {{ $scoreBg }} flex flex-col items-center justify-center shrink-0">
                    <span class="text-3xl font-black {{ $scoreColor }}">{{ $score }}%</span>
                    <span class="text-[9px] font-black text-slate-400">نسبة التوافق</span>
                </div>
                <div class="flex-1 text-center md:text-right">
                    <h3 class="text-lg font-black text-slate-800 mb-1">🎯 تحليل توافقك مع هذه المنحة</h3>
                    @if($matchScore->summary)
                        <p class="text-sm font-bold text-slate-500 leading-relaxed">{{ $matchScore->summary }}</p>
                    @endif
                </div>
            </div>

            @if(!empty($matchScore->matched_criteria) || !empty($matchScore->gaps))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-8 border-t border-slate-50">
                @if(!empty($matchScore->matched_criteria))
                <div>
                    <h4 class="text-xs font-black text-emerald-600 uppercase mb-3">✅ نقاط قوتك</h4>
                    <ul class="space-y-2">
                        @foreach($matchScore->matched_criteria as $point)
                            <li class="text-xs font-bold text-slate-600 flex items-start gap-2">
                                <span class="text-emerald-500 shrink-0">•</span> {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(!empty($matchScore->gaps))
                <div>
                    <h4 class="text-xs font-black text-amber-600 uppercase mb-3">⚠️ عليك الانتباه لـ</h4>
                    <ul class="space-y-2">
                        @foreach($matchScore->gaps as $point)
                            <li class="text-xs font-bold text-slate-600 flex items-start gap-2">
                                <span class="text-amber-500 shrink-0">•</span> {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif

            <p class="text-[10px] font-bold text-slate-300 text-center mt-6">هذا التحليل تقديري ومبني على بيانات ملفك الشخصي، ولا يضمن نتيجة القبول النهائية.</p>
        </div>
        @endif

        {{-- عداد آخر موعد للتقديم --}}
        @if($scholarship->deadline)
        @php
            $deadlineEnd = $scholarship->deadline->copy()->endOfDay();
            $arabicDays = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
            $deadlineLabel = $arabicDays[$deadlineEnd->dayOfWeek] . '، الساعة 11:59 مساءً';
        @endphp
        <div x-data="scholarshipCountdown('{{ $deadlineEnd->toIso8601String() }}')" x-init="init()"
             class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-10 mb-8 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-40 h-40 bg-gold-50 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-60 pointer-events-none"></div>
            <template x-if="!expired">
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-center md:text-right">
                        <span class="inline-flex items-center gap-2 bg-gold-50 text-gold-700 px-4 py-1.5 rounded-full text-[11px] font-black mb-3">
                            <span class="w-1.5 h-1.5 bg-gold-500 rounded-full animate-pulse"></span>
                            آخر موعد للتقديم
                        </span>
                        <h3 class="text-lg md:text-xl font-black text-slate-800">{{ $deadlineLabel }}</h3>
                        <p class="text-xs font-bold text-slate-400 mt-2 max-w-xs">العدّ التنازلي يساعدك على معرفة الوقت المتبقي قبل إغلاق باب التقديم على هذه المنحة.</p>
                    </div>
                    <div class="flex items-center gap-3 md:gap-4">
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl w-16 md:w-20 py-3 text-center">
                            <div class="text-xl md:text-2xl font-black text-navy-900" x-text="pad(days)"></div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1">يوم</div>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl w-16 md:w-20 py-3 text-center">
                            <div class="text-xl md:text-2xl font-black text-navy-900" x-text="pad(hours)"></div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1">ساعة</div>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl w-16 md:w-20 py-3 text-center">
                            <div class="text-xl md:text-2xl font-black text-navy-900" x-text="pad(minutes)"></div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1">دقيقة</div>
                        </div>
                        <div class="bg-gold-600 rounded-2xl w-16 md:w-20 py-3 text-center shadow-lg shadow-gold-100">
                            <div class="text-xl md:text-2xl font-black text-white" x-text="pad(seconds)"></div>
                            <div class="text-[10px] font-bold text-gold-100 mt-1">ثانية</div>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="expired">
                <div class="relative flex items-center justify-center gap-3 text-center py-1">
                    <span class="text-2xl">⏰</span>
                    <p class="text-sm font-black text-rose-500">انتهى موعد التقديم على هذه المنحة</p>
                </div>
            </template>
        </div>
        @endif

        {{-- نظام التبويبات الفوري ومحتويات الأقسام --}}
        <div x-data="{ activeTab: 'overview' }">
            {{-- أزرار التبويبات --}}
            <div class="flex justify-center gap-8 md:gap-12 border-b border-slate-200 mb-8 overflow-x-auto whitespace-nowrap">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">نظرة عامة</button>
                <button @click="activeTab = 'conditions'" :class="activeTab === 'conditions' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">الشروط</button>
                <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">المستندات</button>
                <button @click="activeTab = 'features'" :class="activeTab === 'features' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">المميزات</button>
                <button @click="activeTab = 'application_process'" :class="activeTab === 'application_process' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">آلية التقديم</button>
            </div>

            {{-- حاوية محتويات التبويبات المشروطة --}}
            <div class="space-y-8" x-cloak>
                
                {{-- قسم: نظرة عامة --}}
                <div x-show="activeTab === 'overview'" x-transition>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-50 text-right">
                        <div class="flex items-center justify-end gap-3 mb-6">
                            <h3 class="text-xl font-black text-slate-800">نظرة عامة</h3>
                            <span class="text-xl">📖</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                            @if($scholarship->overview)
                                {!! $scholarship->overview !!}
                            @elseif($scholarship->description)
                                {!! nl2br(e($scholarship->description)) !!}
                            @else
                                <p class="text-slate-400 italic">لا توجد نظرة عامة متاحة لهذه المنحة</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- قسم: الشروط --}}
                <div x-show="activeTab === 'conditions'" x-transition>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-50 text-right">
                        <div class="flex items-center justify-end gap-3 mb-6">
                            <h3 class="text-xl font-black text-slate-800">الشروط والأهلية</h3>
                            <span class="text-xl">✅</span>
                        </div>
                        @if($scholarship->min_gpa !== null)
                        <div class="flex items-center justify-end gap-3 bg-gold-50 border border-gold-100 rounded-2xl px-5 py-4 mb-6">
                            <div class="text-right">
                                <p class="text-[11px] font-black text-gold-700 uppercase tracking-widest">الحد الأدنى للمعدل المطلوب</p>
                                <p class="text-sm font-black text-slate-700 mt-0.5">{{ rtrim(rtrim(number_format($scholarship->min_gpa, 2), '0'), '.') }}%</p>
                            </div>
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-lg">🎓</div>
                        </div>
                        @endif
                        <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                            @if($scholarship->conditions)
                                {!! $scholarship->conditions !!}
                            @else
                                <p class="text-slate-400 italic">الشروط ستُحدد قريباً من قبل الإدارة</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- قسم: المستندات --}}
                <div x-show="activeTab === 'documents'" x-transition>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-50 text-right">
                        <div class="flex items-center justify-end gap-3 mb-6">
                            <h3 class="text-xl font-black text-slate-800">المستندات المطلوبة</h3>
                            <span class="text-xl">📄</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                            @if($scholarship->documents)
                                {!! $scholarship->documents !!}
                            @else
                                <p class="text-slate-400 italic">قائمة المستندات ستُحدد قريباً</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- قسم: المميزات --}}
                <div x-show="activeTab === 'features'" x-transition>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-50 text-right">
                        <div class="flex items-center justify-end gap-3 mb-6">
                            <h3 class="text-xl font-black text-slate-800">المميزات والمزايا</h3>
                            <span class="text-xl">⭐</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                            @if($scholarship->features)
                                {!! $scholarship->features !!}
                            @else
                                <p class="text-slate-400 italic">المميزات ستُحدد قريباً من قبل الإدارة</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- قسم: آلية التقديم --}}
                <div x-show="activeTab === 'application_process'" x-transition>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-50 text-right">
                        <div class="flex items-center justify-end gap-3 mb-6">
                            <h3 class="text-xl font-black text-slate-800">آلية التقديم</h3>
                            <span class="text-xl">🧭</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                            @if($scholarship->application_process)
                                {!! $scholarship->application_process !!}
                            @else
                                <p class="text-slate-400 italic">آلية التقديم ستُحدد قريباً من قبل الإدارة</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
function scholarshipCountdown(deadlineIso) {
    return {
        // ملاحظة: الوقت المعروض بالنص (11:59 مساءً) بيتحسب من السيرفر مباشرة
        // (نفس منطقة زمنية التطبيق)، عشان منتجنبش فروقات التوقيت المحلي للمتصفح.
        // الـ JS هون مسؤول بس عن الأرقام المتحركة (أيام/ساعات/دقايق/ثواني)،
        // وده حساب فرق زمني مطلق فمش متأثر بأي منطقة زمنية.
        deadline: deadlineIso ? new Date(deadlineIso) : null,
        days: 0, hours: 0, minutes: 0, seconds: 0,
        expired: false,
        timer: null,

        init() {
            if (!this.deadline || isNaN(this.deadline.getTime())) {
                this.expired = true;
                return;
            }
            this.tick();
            this.timer = setInterval(() => this.tick(), 1000);
        },

        tick() {
            const diff = this.deadline - new Date();
            if (diff <= 0) {
                this.expired = true;
                if (this.timer) clearInterval(this.timer);
                this.days = this.hours = this.minutes = this.seconds = 0;
                return;
            }
            this.days = Math.floor(diff / 86400000);
            this.hours = Math.floor((diff / 3600000) % 24);
            this.minutes = Math.floor((diff / 60000) % 60);
            this.seconds = Math.floor((diff / 1000) % 60);
        },

        pad(n) {
            return String(n).padStart(2, '0');
        }
    };
}
</script>
@endsection