@extends('layouts.dashboard')

@section('title', 'الملف الشخصي')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10" dir="rtl" x-data="profileData()" x-init="initProfile()">
    <div class="max-w-6xl mx-auto">
        
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50 mb-8">
            <div class="flex justify-between items-center mb-4">
                <div class="text-right">
                    <h1 class="text-3xl font-black bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text text-transparent animate-pulse mb-2">الملف الشخصي</h1>
                    <p class="text-slate-400 text-xs font-bold">🎯 أكمل ملفك الشخصي للحصول على توصيات منح دراسية مخصصة أفضل</p>
                </div>
                <div class="text-center">
                    <span class="text-4xl lg:text-5xl font-black text-gold-600 drop-shadow-lg">{{ $profileCompletion }}%</span>
                    <div class="w-full bg-gradient-to-r from-slate-200 to-slate-300 h-4 rounded-full overflow-hidden mt-2 shadow-inner">
                        <div class="bg-gradient-to-r from-gold-500 to-gold-600 h-full transition-all duration-1000 shadow-lg" :style="'width: ' + completion() + '%'"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 font-bold" x-text="completionStatus()"></p>
                </div>
            </div>
        </div>

        <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="flex-1 order-2 lg:order-1">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 overflow-hidden">
                        
                        <div class="flex border-b border-slate-100 bg-slate-50/50 p-2">
                            <button type="button" @click="tab = 'personal'" :class="tab === 'personal' ? 'bg-white shadow-sm text-gold-600 border-r-4 border-gold-500' : 'text-slate-400 hover:text-slate-600'" class="flex-1 py-3 rounded-2xl font-black text-sm transition-all duration-300">
                                معلومات شخصية
                            </button>
                            <button type="button" @click="tab = 'education'" :class="tab === 'education' ? 'bg-white shadow-sm text-gold-600 border-r-4 border-gold-500' : 'text-slate-400 hover:text-slate-600'" class="flex-1 py-3 rounded-2xl font-bold text-sm transition-all duration-300">
                                تعليم
                            </button>
                            <button type="button" @click="tab = 'languages'" :class="tab === 'languages' ? 'bg-white shadow-sm text-gold-600 border-r-4 border-gold-500' : 'text-slate-400 hover:text-slate-600'" class="flex-1 py-3 rounded-2xl font-black text-sm transition-all duration-300">
                                لغات
                            </button>
                            <button type="button" @click="tab = 'documents'" :class="tab === 'documents' ? 'bg-white shadow-sm text-gold-600 border-r-4 border-gold-500' : 'text-slate-400 hover:text-slate-600'" class="flex-1 py-3 rounded-2xl font-black text-sm transition-all duration-300">
                                مستندات
                            </button>
                        </div>

                        <div class="p-8">
                            
<!-- Tab: Personal Information -->
                            <div x-show="tab === 'personal'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-slate-500 font-bold mb-2 text-sm">الاسم الكامل</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 font-bold mb-2 text-sm">البريد الإلكتروني</label>
                                            <input type="email" value="{{ $user->email }}" readonly class="w-full bg-slate-100 border-0 rounded-2xl p-4 text-slate-500 font-bold cursor-not-allowed">
                                            <p class="text-[10px] text-slate-400 mt-1">لا يمكن تغيير البريد الإلكتروني</p>
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 font-bold mb-2 text-sm">رقم الهاتف</label>
                                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+966 50 123 4567" dir="ltr" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-700 font-bold text-right focus:ring-2 focus:ring-gold-500">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 font-bold mb-2 text-sm">تاريخ الميلاد</label>
                                            <input type="date" name="birthdate" value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 font-bold mb-2 text-sm">الدولة</label>
                                            <input type="text" name="country" value="{{ old('country', $user->country) }}" placeholder="المملكة العربية السعودية" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 font-bold mb-2 text-sm">المدينة</label>
                                            <input type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="الرياض" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                        </div>
                                    </div>
                                    
                                    <!-- IDs Section -->
                                    <div class="bg-gold-100 border border-gold-100 rounded-3xl p-6">
                                        <h3 class="text-gold-600 font-black mb-4">المعلومات التعريفية</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">رقم الهوية</label>
                                                <input type="text" name="national_id" value="{{ old('national_id', $user->national_id) }}" placeholder="رقم الهوية الشخصية" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">رقم جواز السفر</label>
                                                <input type="text" name="passport_number" value="{{ old('passport_number', $user->passport_number) }}" placeholder="رقم جواز السفر" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">تاريخ انتهاء جواز السفر</label>
                                                <input type="date" name="passport_expiry" value="{{ old('passport_expiry', $user->passport_expiry?->format('Y-m-d')) }}" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">دولة إصدار جواز السفر</label>
                                                <input type="text" name="passport_country" value="{{ old('passport_country', $user->passport_country) }}" placeholder="الدولة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

<!-- Tab: Education -->
                            <div x-show="tab === 'education'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                                <div class="space-y-8">
                                    
                                    <!-- High School (Required) -->
                                    <div class="bg-red-50 border border-red-100 rounded-3xl p-6">
                                        <h3 class="text-red-600 font-black mb-4 flex items-center gap-2">
                                            <span class="text-xs">*</span> الثانوية العامة (إلزامي)
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">المدرسة</label>
                                                <input type="text" name="high_school_name" value="{{ old('high_school_name', $user->high_school_name) }}" placeholder="اسم المدرسة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-red-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">الدولة</label>
                                                <input type="text" name="high_school_country" value="{{ old('high_school_country', $user->high_school_country) }}" placeholder="الدولة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-red-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">سنة التخرج</label>
                                                <input type="number" name="high_school_year" value="{{ old('high_school_year', $user->high_school_year) }}" placeholder="سنة التخرج" min="1950" max="2030" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-red-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">التخصص/الفرع</label>
                                                <input type="text" name="high_school_branch" value="{{ old('high_school_branch', $user->high_school_branch) }}" placeholder="علمي / أدبي / تجاري" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-red-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">المعدل (GPA)</label>
                                                <input type="number" name="high_school_gpa" value="{{ old('high_school_gpa', $user->high_school_gpa) }}" placeholder="المعدل" step="0.01" min="0" max="100" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-red-500">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Diploma (Optional) -->
                                    <div class="bg-amber-50 border border-amber-100 rounded-3xl p-6" x-data="{ has_diploma: {{ $user->diploma_institute ? 'true' : 'false' }} }">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-amber-600 font-black">الدبلوم</h3>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="has_diploma" x-model="has_diploma" class="w-4 h-4 text-amber-600 rounded">
                                                <span class="text-amber-700 text-xs font-bold">لديك بلوم</span>
                                            </label>
                                        </div>
                                        <div x-show="has_diploma" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">المعهد/الجامعة</label>
                                                <input type="text" name="diploma_institute" value="{{ old('diploma_institute', $user->diploma_institute) }}" placeholder="اسم المعهد" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">الدولة</label>
                                                <input type="text" name="diploma_country" value="{{ old('diploma_country', $user->diploma_country) }}" placeholder="الدولة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">سنة التخرج</label>
                                                <input type="number" name="diploma_year" value="{{ old('diploma_year', $user->diploma_year) }}" placeholder="سنة التخرج" min="1950" max="2030" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">التخصص</label>
                                                <input type="text" name="diploma_degree" value="{{ old('diploma_degree', $user->diploma_degree) }}" placeholder="التخصص" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-amber-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">المعدل (GPA)</label>
                                                <input type="number" name="diploma_gpa" value="{{ old('diploma_gpa', $user->diploma_gpa) }}" placeholder="المعدل" step="0.01" min="0" max="4" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-amber-500">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bachelor (Optional) -->
                                    <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6" x-data="{ has_bachelor: {{ $user->bachelor_university ? 'true' : 'false' }} }">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-blue-600 font-black">البكالوريوس</h3>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="has_bachelor" x-model="has_bachelor" class="w-4 h-4 text-blue-600 rounded">
                                                <span class="text-blue-700 text-xs font-bold">لديك بكالوريوس</span>
                                            </label>
                                        </div>
                                        <div x-show="has_bachelor" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">الجامعة</label>
                                                <input type="text" name="bachelor_university" value="{{ old('bachelor_university', $user->bachelor_university) }}" placeholder="اسم الجامعة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">الدولة</label>
                                                <input type="text" name="bachelor_country" value="{{ old('bachelor_country', $user->bachelor_country) }}" placeholder="الدولة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">سنة التخرج</label>
                                                <input type="number" name="bachelor_year" value="{{ old('bachelor_year', $user->bachelor_year) }}" placeholder="سنة التخرج" min="1950" max="2030" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">التخصص</label>
                                                <input type="text" name="bachelor_degree" value="{{ old('bachelor_degree', $user->bachelor_degree) }}" placeholder="التخصص" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">المعدل (GPA)</label>
                                                <input type="number" name="bachelor_gpa" value="{{ old('bachelor_gpa', $user->bachelor_gpa) }}" placeholder="المعدل" step="0.01" min="0" max="4" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Master (Optional) -->
                                    <div class="bg-gold-100 border border-gold-100 rounded-3xl p-6" x-data="{ has_master: {{ $user->master_university ? 'true' : 'false' }} }">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-gold-600 font-black">الماجستير</h3>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="has_master" x-model="has_master" class="w-4 h-4 text-gold-600 rounded">
                                                <span class="text-gold-700 text-xs font-bold">لديك ماجستير</span>
                                            </label>
                                        </div>
                                        <div x-show="has_master" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">الجامعة</label>
                                                <input type="text" name="master_university" value="{{ old('master_university', $user->master_university) }}" placeholder="اسم الجامعة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">الدولة</label>
                                                <input type="text" name="master_country" value="{{ old('master_country', $user->master_country) }}" placeholder="الدولة" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">سنة التخرج</label>
                                                <input type="number" name="master_year" value="{{ old('master_year', $user->master_year) }}" placeholder="سنة التخرج" min="1950" max="2030" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">التخصص</label>
                                                <input type="text" name="master_degree" value="{{ old('master_degree', $user->master_degree) }}" placeholder="التخصص" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-500 font-bold mb-2 text-xs">المعدل (GPA)</label>
                                                <input type="number" name="master_gpa" value="{{ old('master_gpa', $user->master_gpa) }}" placeholder="المعدل" step="0.01" min="0" max="4" class="w-full bg-white border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Tab: Languages -->
                            <div x-show="tab === 'languages'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                                <div class="space-y-4">
                                    @php
                                    $langs = $user->languages ?? [['name' => 'العربية', 'cert' => 'لغة الأم']];
                                    @endphp
                                    
                                    @foreach($langs as $index => $lang)
                                    <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl">
                                        <input type="text" name="languages[{{ $index }}][name]" value="{{ $lang['name'] ?? '' }}" placeholder="اللغة" class="bg-transparent border-0 p-2 text-slate-700 font-bold w-1/3">
                                        <input type="text" name="languages[{{ $index }}][cert]" value="{{ $lang['cert'] ?? '' }}" placeholder="الشهادة" class="bg-transparent border-0 p-2 text-slate-500 font-bold text-right w-1/3">
                                    </div>
                                    @endforeach
                                </div>
                            </div>

<div x-show="tab === 'documents'" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 transform scale-95" 
     x-transition:enter-end="opacity-100 transform scale-100" 
     class="space-y-8">

    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-slate-800 font-black">المستندات الإلزامية</h3>
                <p class="text-slate-400 text-[10px] font-bold">هذه الملفات ضرورية لإكمال طلبات التقديم</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $reqDocs = [
                    'passport' => ['label' => 'جواز السفر', 'icon' => '🌍', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                    'national_id' => ['label' => 'الهوية الشخصية', 'icon' => '🆔', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                    'high_school_cert' => ['label' => 'شهادة الثانوية', 'icon' => '🎓', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                    'birth_cert' => ['label' => 'شهادة الميلاد', 'icon' => '👶', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                    'cv' => ['label' => 'السيرة الذاتية (CV)', 'icon' => '📄', 'accept' => '.pdf,.doc,.docx', 'class' => 'col-span-1 md:col-span-2'],
                ];
            @endphp

            @foreach($reqDocs as $key => $doc)
            <div class="relative group {{ $doc['class'] ?? '' }}">
                <label class="block cursor-pointer">
                    <input type="file" name="docs[{{ $key }}]" @change="handleDocChange('required', '{{ $key }}', $event)" class="hidden" accept="{{ $doc['accept'] }}">
                    
                    <div :class="isDocUploaded('required', '{{ $key }}') ? 'border-green-500 bg-green-50/30' : 'border-slate-200 bg-slate-50/50'" 
                         class="border-2 border-dashed rounded-2xl p-4 transition-all duration-300 group-hover:border-gold-400 group-hover:bg-white">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $doc['icon'] }}</span>
                                <div>
                                    <p class="text-slate-700 font-black text-xs">{{ $doc['label'] }}</p>
                                    <p class="text-[9px] font-bold" :class="isDocUploaded('required', '{{ $key }}') ? 'text-green-600' : 'text-slate-400'" 
                                       x-text="isDocUploaded('required', '{{ $key }}') ? '✓ جاهز للمراجعة' : 'اضغط للرفع'"></p>
                                </div>
                            </div>
                            
                            <template x-if="isDocUploaded('required', '{{ $key }}')">
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </template>
                        </div>

                        <div x-show="temp_required_docs['{{ $key }}']" class="mt-3 flex items-center justify-between bg-white/80 p-2 rounded-xl border border-green-100">
                            <span class="text-[10px] text-gold-600 font-bold truncate flex-1">📎 <span x-text="temp_required_docs['{{ $key }}']"></span></span>
                            <button type="button" @click.prevent="removeTempDoc('required', '{{ $key }}')" class="text-red-500 hover:scale-110 px-2 font-black text-lg">×</button>
                        </div>
                    </div>
                </label>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <div>
                <h3 class="text-slate-800 font-black">المستندات الاختيارية</h3>
                <p class="text-slate-400 text-[10px] font-bold">إضافتها تزيد من قوة ملفك الأكاديمي</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $optDocs = [
                    'language_cert' => ['label' => 'شهادة لغة', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                    'courses_cert' => ['label' => 'شهادة دورات', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                    'recommendation' => ['label' => 'خطاب توصية', 'accept' => '.pdf,.doc,.docx'],
                    'intent_letter' => ['label' => 'خطاب نية', 'accept' => '.pdf,.doc,.docx'],
                ];
            @endphp
            @foreach($optDocs as $key => $doc)
            <div class="relative group">
                <label class="block cursor-pointer">
                    <input type="file" name="docs[{{ $key }}]" @change="handleDocChange('optional', '{{ $key }}', $event)" class="hidden" accept="{{ $doc['accept'] }}">
                    <div :class="isDocUploaded('optional', '{{ $key }}') ? 'border-amber-400 bg-amber-50/30' : 'border-slate-200 bg-slate-50/50'" 
                         class="border-2 border-dashed rounded-2xl p-4 transition-all duration-300 group-hover:border-gold-400 group-hover:bg-white">
                        
                        <div class="flex items-center justify-between">
                            <span class="text-slate-700 font-black text-xs">{{ $doc['label'] }}</span>
                            <span x-show="isDocUploaded('optional', '{{ $key }}')" class="text-amber-600 text-[10px] font-black">✓ مرفوع</span>
                            <span x-show="!isDocUploaded('optional', '{{ $key }}')" class="text-slate-300 text-lg">+</span>
                        </div>

                        <div x-show="temp_optional_docs['{{ $key }}']" class="mt-2 flex items-center justify-between bg-white/80 p-1.5 rounded-lg border border-amber-100">
                            <span class="text-[9px] text-amber-700 font-bold truncate max-w-[120px]" x-text="temp_optional_docs['{{ $key }}']"></span>
                            <button type="button" @click.prevent="removeTempDoc('optional', '{{ $key }}')" class="text-red-500 font-black px-1">×</button>
                        </div>
                    </div>
                </label>
            </div>
            @endforeach
        </div>
    </div>
</div>

                            <div class="mt-10 pt-6 border-t border-slate-100">
                            <button type="submit" :disabled="saving" @click="showToast('success', 'جاري حفظ التغييرات...')" 
                                class="bg-gradient-to-r from-gold-600 to-gold-700 text-white px-12 py-4 rounded-3xl font-black shadow-xl shadow-navy-100 hover:shadow-gold-300 hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!saving">
                                        <svg class="w-5 h-5 inline -ml-1 mr-2" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        حفظ التغييرات
                                    </span>
                                    <span x-show="saving" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        جاري الحفظ...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="w-full lg:w-80 order-1 lg:order-2">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50 text-center sticky top-8">
                        <div class="relative w-32 h-32 mx-auto mb-6 group">
                            <div id="avatarDisplay">
                            @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg">
                            @else
                            <div class="w-full h-full rounded-full bg-gradient-to-tr from-gold-600 to-gold-400 flex items-center justify-center text-white text-4xl font-black shadow-xl border-4 border-white">
                                {{ Str::substr($user->name, 0, 1) }}
                            </div>
                            @endif
                            </div>
                            
                            <!-- Camera Icon for Uploading -->
                            <label for="avatar_upload" class="absolute -bottom-2 -right-2 bg-gold-600 text-white p-3 rounded-full cursor-pointer shadow-lg hover:bg-gold-700 transition-all hover:scale-110 border-4 border-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </label>
                            <input type="file" id="avatar_upload" name="avatar" class="hidden" accept="image/*">
                        </div>
                        <h2 class="text-xl font-black text-slate-800 mb-1">{{ $user->name }}</h2>
                        <p class="text-slate-400 text-xs font-bold mb-8">{{ $user->degree ?? 'طالب' }}</p>

                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-2xl flex justify-between items-center">
                                <span class="text-blue-600 font-black">{{ $user->favoriteScholarships()->count() }}</span>
                                <span class="text-blue-900 text-[10px] font-bold">منح محفوظة</span>
                            </div>
                            <div class="bg-gold-100 p-4 rounded-2xl flex justify-between items-center">
                                <span class="text-gold-600 font-black">{{ $user->orders()->where('status', 'pending')->count() }}</span>
                                <span class="text-navy-900 text-[10px] font-bold">طلبات نشطة</span>
                            </div>
                            <div class="bg-green-50 p-4 rounded-2xl flex justify-between items-center">
                                <span class="text-green-600 font-black">{{ $user->orders()->where('status', 'paid')->count() }}</span>
                                <span class="text-green-900 text-[10px] font-bold">مدفوعة</span>
                            </div>
                        </div>

                        @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mt-6 p-3 bg-green-500 text-white rounded-xl text-xs font-bold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
function profileData() {
    return {
        tab: localStorage.getItem('profileTab') || 'personal',
        saving: false,
        initialCompletion: {{ $profileCompletion }},
        completion: {{ $profileCompletion }},
        server_required_docs: {!! json_encode(array_map(fn($path) => basename($path), array_filter((array)($user ?? new stdClass())->required_documents ?? []))) !!},
        server_optional_docs: {!! json_encode(array_map(fn($path) => basename($path), array_filter((array)($user ?? new stdClass())->optional_documents ?? []))) !!},
        temp_required_docs: {},
        temp_optional_docs: {},
        avatarCropper: null,
        avatarFile: null,
        avatarPreview: null,

        initProfile() {
            this.tab = localStorage.getItem('profileTab') || 'personal';
            if (this.initialCompletion >= 80) {
                this.celebrateCompletion();
            }
            this.$watch('tab', (value) => localStorage.setItem('profileTab', value));
            this.updateCompletionOnInput();
            this.initAvatarCropper();
        },

        completion() {
            return Math.min(100, this.initialCompletion + this.calculateProgressBoost());
        },

        completionDisplay() {
            return Math.round(this.completion());
        },

        completionStatus() {
            const pct = this.completion();
            if (pct >= 90) return '🏆 ممتاز! جاهز للتقديم';
            if (pct >= 70) return '⭐ جيد جداً';
            if (pct >= 50) return '👍 جيد';
            return '📈 تحتاج تحسين';
        },

        calculateProgressBoost() {
            let boost = 0;
            // Simple form fill detection
            const filledFields = document.querySelectorAll('input:not([readonly])[value]:not([value=""])').length;
            boost += Math.min(filledFields * 2, 20);
            // Documents
            boost += Object.keys(this.temp_required_docs).length * 5;
            boost += Object.keys(this.server_required_docs).length * 3;
            return boost;
        },

        updateCompletionOnInput() {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    this.$nextTick(() => {
                        // Trigger reactivity
                    });
                });
            });
        },

        celebrateCompletion() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#4F46E5', '#7C3AED', '#06B6D4', '#10B981']
            });
            this.showToast('success', 'تهانينا! ملفك الشخصي مكتمل بنسبة عالية 🎉');
        },

        showToast(type, message) {
            Alpine.store('toast').showToast(type, message);
        },

        initAvatarCropper() {
            const avatarInput = document.getElementById('avatar_upload');
            if (avatarInput) {
                avatarInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        this.avatarFile = file;
                        this.previewAvatar(file);
                    }
                });
            }
        },

        previewAvatar(file) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                document.getElementById('avatarDisplay').innerHTML =
                    `<img src="${ev.target.result}" alt="Avatar" class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg">`;
            };
            reader.readAsDataURL(file);
            this.showToast('success', 'تم اختيار الصورة، اضغط "حفظ" لتأكيد التحديث');
        },

        quickSave() {
            const formData = new FormData(document.querySelector('form'));
            // Simulate quick save of changed fields only
            this.showToast('success', 'تم الحفظ السريع بنجاح ✅');
        },

        handleDocChange(docType, key, event) {
            const input = event.target;
            if (input.files.length > 0) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    this.showToast('error', 'حجم الملف كبير جداً (5MB max)');
                    input.value = '';
                    return;
                }
                this[`temp_${docType}_docs`][key] = file.name;
                this.showToast('success', `تم رفع ${file.name}`);
            } else {
                delete this[`temp_${docType}_docs`][key];
            }
            this.$nextTick(() => this.updateCompletionOnInput());
        },

        removeTempDoc(docType, key) {
            delete this[`temp_${docType}_docs`][key];
            const input = document.querySelector(`input[name="docs[${key}]"]`);
            if (input) input.value = '';
            this.showToast('info', 'تم إزالة الملف');
        },

        isDocUploaded(docType, key) {
            const server_docs = docType === 'required' ? this.server_required_docs : this.server_optional_docs;
            const temp_docs = this[`temp_${docType}_docs`];
            return !!server_docs[key] || !!temp_docs[key];
        }
    }
}
</script>
@endsection
