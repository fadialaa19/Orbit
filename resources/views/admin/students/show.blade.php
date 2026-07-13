@extends('layouts.admin')

@section('title', 'ملف الطالب')
@section('breadcrumb', $student->name)

@php
    $requiredDocLabels = [
        'passport' => 'جواز السفر',
        'national_id' => 'الهوية الوطنية',
        'high_school_cert' => 'شهادة الثانوية',
        'birth_cert' => 'شهادة الميلاد',
        'cv' => 'السيرة الذاتية',
    ];
    $optionalDocLabels = [
        'language_cert' => 'شهادة لغة',
        'courses_cert' => 'شهادة دورات',
        'recommendation' => 'خطاب توصية',
        'intent_letter' => 'خطاب النوايا',
    ];
    $requiredDocs = $student->required_documents ?? [];
    $optionalDocs = $student->optional_documents ?? [];
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.students.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-gold-600 font-bold transition text-sm">
            <span>العودة لقائمة الطلاب</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    {{-- بطاقة رأس الملف --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8 flex flex-col md:flex-row items-center md:items-start gap-6 text-center md:text-right">
        @if($student->avatar)
            <img src="{{ $student->avatar }}" alt="{{ $student->name }}" class="w-24 h-24 rounded-2xl object-cover shadow-md">
        @else
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-gold-600 to-gold-400 flex items-center justify-center text-white text-3xl font-black shadow-md">
                {{ mb_substr($student->name, 0, 1) }}
            </div>
        @endif

        <div class="flex-1">
            <h1 class="text-2xl font-black text-slate-900">{{ $student->name }}</h1>
            @if($student->name_en)
                <p class="text-slate-400 font-bold text-sm">{{ $student->name_en }}</p>
            @endif
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-3">
                @php
                    $statusMap = [
                        'active' => ['bg-emerald-100 text-emerald-700', 'نشط'],
                        'pending' => ['bg-amber-100 text-amber-700', 'بانتظار المراجعة'],
                        'inactive' => ['bg-rose-100 text-rose-700', 'معطل'],
                    ];
                    $current = $statusMap[$student->status] ?? ['bg-slate-100 text-slate-500', 'غير معروف'];
                @endphp
                <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $current[0] }}">{{ $current[1] }}</span>
                @if($student->email_verified_at)
                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600">إيميل مفعّل</span>
                @else
                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-500">إيميل غير مفعّل</span>
                @endif
                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-gold-50 text-gold-600">نسبة اكتمال الملف: {{ $student->profile_completion ?? 0 }}%</span>
            </div>
        </div>

        <div class="text-sm text-slate-500 font-bold space-y-1 md:text-left">
            <p>{{ $student->email }}</p>
            @if($student->phone)
                <p dir="ltr">{{ $student->phone }}</p>
            @endif
            <p class="text-xs text-slate-400">عضو منذ {{ $student->created_at->format('Y-m-d') }}</p>
        </div>
    </div>

    {{-- البيانات الشخصية --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
        <h2 class="text-lg font-black text-slate-900 mb-6">البيانات الشخصية</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                'الجنس' => $student->gender,
                'تاريخ الميلاد' => optional($student->birthdate)->format('Y-m-d'),
                'الدولة' => $student->country,
                'المدينة' => $student->city,
                'رقم الهوية الوطنية' => $student->national_id,
                'رقم جواز السفر' => $student->passport_number,
                'دولة إصدار الجواز' => $student->passport_country,
                'تاريخ انتهاء الجواز' => optional($student->passport_expiry)->format('Y-m-d'),
            ] as $label => $value)
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100/50">
                    <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">{{ $label }}</p>
                    <p class="text-sm font-black text-slate-700">{{ $value ?: '—' }}</p>
                </div>
            @endforeach
        </div>
        @if($student->bio)
            <div class="mt-6">
                <p class="text-[10px] text-slate-400 font-bold uppercase mb-2">نبذة</p>
                <p class="text-sm font-bold text-slate-600 leading-relaxed">{{ $student->bio }}</p>
            </div>
        @endif
    </div>

    {{-- المسيرة التعليمية --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8 space-y-8">
        <h2 class="text-lg font-black text-slate-900">المسيرة التعليمية</h2>

        <div>
            <h3 class="text-sm font-black text-gold-600 mb-3">الثانوية العامة</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    'المدرسة' => $student->high_school_name,
                    'الدولة' => $student->high_school_country,
                    'سنة التخرج' => $student->high_school_year,
                    'الفرع' => $student->high_school_branch,
                    'المعدل' => $student->high_school_gpa,
                ] as $label => $value)
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100/50">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">{{ $label }}</p>
                        <p class="text-sm font-black text-slate-700">{{ $value ?: '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($student->diploma_institute || $student->diploma_degree)
        <div>
            <h3 class="text-sm font-black text-gold-600 mb-3">الدبلوم</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    'المعهد' => $student->diploma_institute,
                    'الدولة' => $student->diploma_country,
                    'التخصص' => $student->diploma_degree,
                    'سنة التخرج' => $student->diploma_year,
                    'المعدل' => $student->diploma_gpa,
                ] as $label => $value)
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100/50">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">{{ $label }}</p>
                        <p class="text-sm font-black text-slate-700">{{ $value ?: '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($student->bachelor_university || $student->bachelor_degree)
        <div>
            <h3 class="text-sm font-black text-gold-600 mb-3">البكالوريوس</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    'الجامعة' => $student->bachelor_university,
                    'الدولة' => $student->bachelor_country,
                    'التخصص' => $student->bachelor_degree,
                    'سنة التخرج' => $student->bachelor_year,
                    'المعدل' => $student->bachelor_gpa,
                ] as $label => $value)
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100/50">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">{{ $label }}</p>
                        <p class="text-sm font-black text-slate-700">{{ $value ?: '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($student->master_university || $student->master_degree)
        <div>
            <h3 class="text-sm font-black text-gold-600 mb-3">الماجستير</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    'الجامعة' => $student->master_university,
                    'الدولة' => $student->master_country,
                    'التخصص' => $student->master_degree,
                    'سنة التخرج' => $student->master_year,
                    'المعدل' => $student->master_gpa,
                ] as $label => $value)
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100/50">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">{{ $label }}</p>
                        <p class="text-sm font-black text-slate-700">{{ $value ?: '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(is_array($student->languages) && count($student->languages))
        <div>
            <h3 class="text-sm font-black text-gold-600 mb-3">اللغات</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($student->languages as $lang)
                    <span class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-slate-600">
                        {{ $lang['name'] ?? '' }} @if(!empty($lang['cert'])) ({{ $lang['cert'] }}) @endif
                    </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- المستندات --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8">
        <h2 class="text-lg font-black text-slate-900 mb-6">المستندات المرفوعة</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($requiredDocLabels as $key => $label)
                <div class="flex items-center justify-between bg-slate-50 rounded-xl p-4 border border-slate-100/50">
                    <span class="text-sm font-bold text-slate-600">{{ $label }}</span>
                    @if(!empty($requiredDocs[$key]))
                        <a href="{{ \Storage::disk('public')->url($requiredDocs[$key]) }}" target="_blank" class="text-xs font-black text-gold-600 hover:underline">عرض ↗</a>
                    @else
                        <span class="text-xs font-bold text-slate-300">لم يُرفع</span>
                    @endif
                </div>
            @endforeach
            @foreach($optionalDocLabels as $key => $label)
                <div class="flex items-center justify-between bg-slate-50 rounded-xl p-4 border border-slate-100/50">
                    <span class="text-sm font-bold text-slate-600">{{ $label }}</span>
                    @if(!empty($optionalDocs[$key]))
                        <a href="{{ \Storage::disk('public')->url($optionalDocs[$key]) }}" target="_blank" class="text-xs font-black text-gold-600 hover:underline">عرض ↗</a>
                    @else
                        <span class="text-xs font-bold text-slate-300">لم يُرفع</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
