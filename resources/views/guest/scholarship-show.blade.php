@extends('layouts.app')

@section('title', $scholarship->title_ar . ' - Orbit')
@section('meta_description', $scholarship->university . ' - ' . $scholarship->title_ar)

@section('content')
<style>.prose img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 1rem 0; }</style>
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10">
    <div class="max-w-5xl mx-auto">

        {{-- زر العودة لصفحة المنح --}}
        <div class="flex justify-end mb-6">
            <a href="{{ route('guest.scholarships') }}" class="flex items-center gap-2 text-slate-500 hover:text-gold-600 font-bold transition">
                <span>العودة للمنح</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        {{-- حاوية الكرت الرئيسي للمنحة شاملة الكفر والتفاصيل --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden mb-8">

            {{-- كفر المنحة --}}
            <div class="relative w-full h-48 md:h-64 bg-slate-100">
                @if($scholarship->main_image)
                    <img src="{{ $scholarship->main_image }}" alt="{{ $scholarship->title_ar }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-r from-navy-900 to-navy-800"></div>
                @endif
                <div class="absolute inset-0 bg-black/10"></div>
            </div>

            <div class="p-8 relative">
                <div class="flex flex-col md:flex-row gap-10 items-start">

                    {{-- القسم الجانبي: لوجو المنحة وأزرار التقديم (تتطلب تسجيل الدخول) --}}
                    <div class="w-full md:w-48 flex flex-col items-center gap-6">
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

                        <div class="w-full space-y-3">
                            @if($scholarship->application_url)
                                <a href="{{ route('login') }}"
                                   class="w-full block bg-gold-600 text-white py-3.5 rounded-2xl font-black text-sm text-center shadow-lg shadow-gold-100 hover:bg-gold-700 hover:scale-[1.01] transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>سجّل دخول للتقديم مجاناً</span>
                                </a>
                            @endif

                            @if($scholarship->apply_via_us_link)
                                <a href="{{ route('login') }}"
                                   class="w-full block bg-navy-900 text-white py-3.5 rounded-2xl font-black text-sm text-center shadow-lg hover:bg-navy-800 hover:scale-[1.01] transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>سجّل دخول للتقديم عن طريقنا</span>
                                </a>
                            @endif

                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center gap-2 border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 py-3 rounded-2xl font-black text-sm transition">
                                <span>ليس لديك حساب؟ سجّل مجاناً</span>
                            </a>
                        </div>
                    </div>

                    {{-- القسم الأساسي: المسميات والبيانات المفصلة للمنحة --}}
                    <div class="flex-1 text-right w-full">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-end gap-3 mb-2">
                            <span class="bg-gold-100 text-gold-600 px-3 py-1 rounded-lg text-xs font-black self-start sm:self-auto order-2 sm:order-1">
                                @switch($scholarship->category)
                                    @case('Bachelor') بكالوريوس @break
                                    @case('Master') ماجستير @break
                                    @case('PhD') دكتوراه @break
                                    @case('Short Course') كورس قصير @break
                                    @default {{ $scholarship->category ?? 'منحة دراسية' }}
                                @endswitch
                            </span>
                            <h1 class="text-2xl md:text-3xl font-black text-slate-800 order-1 sm:order-2">{{ $scholarship->title_ar }}</h1>
                        </div>
                        <p class="text-slate-400 font-bold mb-8 text-base">{{ $scholarship->university }}</p>

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
                                        @switch($scholarship->category)
                                            @case('Bachelor') بكالوريوس @break
                                            @case('Master') ماجستير @break
                                            @case('PhD') دكتوراه @break
                                            @case('Short Course') كورس قصير @break
                                            @default {{ $scholarship->category ?? 'كل المراحل' }}
                                        @endswitch
                                    </p>
                                </div>
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-gold-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-3">
                            @foreach($scholarship->recommended_tags ?? [] as $tag)
                                <span class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded-full text-xs font-black">🔥 {{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- نظام التبويبات --}}
        <div x-data="{ activeTab: 'overview' }">
            <div class="flex justify-center gap-8 md:gap-12 border-b border-slate-200 mb-8 overflow-x-auto whitespace-nowrap">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">نظرة عامة</button>
                <button @click="activeTab = 'conditions'" :class="activeTab === 'conditions' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">الشروط</button>
                <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">المستندات</button>
                <button @click="activeTab = 'features'" :class="activeTab === 'features' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">المميزات</button>
                <button @click="activeTab = 'application_process'" :class="activeTab === 'application_process' ? 'pb-4 border-b-2 border-gold-600 text-gold-600 font-black text-sm' : 'pb-4 text-slate-400 font-bold text-sm hover:text-slate-600 transition'" class="cursor-pointer">آلية التقديم</button>
            </div>

            <div class="space-y-8" x-cloak>
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

                <div x-show="activeTab === 'conditions'" x-transition>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-50 text-right">
                        <div class="flex items-center justify-end gap-3 mb-6">
                            <h3 class="text-xl font-black text-slate-800">الشروط والأهلية</h3>
                            <span class="text-xl">✅</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                            @if($scholarship->conditions)
                                {!! $scholarship->conditions !!}
                            @else
                                <p class="text-slate-400 italic">الشروط ستُحدد قريباً من قبل الإدارة</p>
                            @endif
                        </div>
                    </div>
                </div>

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
@endsection
