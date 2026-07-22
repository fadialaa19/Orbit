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

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl p-5 mb-6 font-bold text-sm">
                <p class="font-black mb-2">تعذّر حفظ التعديلات:</p>
                <ul class="list-disc pr-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                                    <template x-for="(lang, index) in languages" :key="index">
                                        <div class="flex items-center gap-3 p-5 bg-slate-50 rounded-3xl">
                                            <input type="text" :name="'languages[' + index + '][name]'" x-model="lang.name" placeholder="اللغة" class="bg-white border border-slate-100 rounded-2xl p-3 text-slate-700 font-bold flex-1 focus:ring-2 focus:ring-gold-500 outline-none">
                                            <select :name="'languages[' + index + '][level]'" x-model="lang.level" class="bg-white border border-slate-100 rounded-2xl p-3 text-slate-700 font-bold flex-1 focus:ring-2 focus:ring-gold-500 outline-none">
                                                <option value="">المستوى</option>
                                                <option value="لغة الأم">لغة الأم</option>
                                                <option value="ممتاز">ممتاز</option>
                                                <option value="جيد جداً">جيد جداً</option>
                                                <option value="جيد">جيد</option>
                                                <option value="متوسط">متوسط</option>
                                                <option value="مبتدئ">مبتدئ</option>
                                            </select>
                                            <button type="button" @click="removeLanguage(index)" class="p-2 text-slate-400 hover:text-rose-600 transition flex-shrink-0" title="حذف">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>

                                    <button type="button" @click="addLanguage()" class="w-full flex items-center justify-center gap-2 border-2 border-dashed border-slate-200 text-slate-500 hover:border-gold-400 hover:text-gold-600 py-3 rounded-2xl font-black text-sm transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>إضافة لغة</span>
                                    </button>
                                </div>
                            </div>

<div x-show="tab === 'documents'"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="space-y-8">

    {{-- المستندات الإلزامية --}}
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
            <template x-for="doc in requiredDocsList" :key="doc.key">
                <div class="border-2 rounded-2xl p-4 transition-all duration-300" :class="doc.uploaded ? statusBorderClass(doc.status) : 'border-dashed border-slate-200 bg-slate-50/50'">
                    <template x-if="!doc.uploaded">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="flex items-center gap-3">
                                <span class="text-2xl" x-text="doc.icon"></span>
                                <span class="text-slate-700 font-black text-xs" x-text="doc.label"></span>
                            </span>
                            <span class="text-slate-300 text-lg">+</span>
                            <input type="file" class="hidden" :accept="doc.accept" @change="uploadCategoryDocument(doc.key, doc.label, $event)">
                        </label>
                    </template>
                    <template x-if="doc.uploaded">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-700 font-black text-xs" x-text="doc.label"></span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full" :class="statusBadgeClass(doc.status)" x-text="statusLabel(doc.status)"></span>
                            </div>
                            <p x-show="doc.status === 'rejected' && doc.admin_note" class="text-[10px] text-rose-500 font-bold" x-text="'سبب الرفض: ' + doc.admin_note"></p>
                            <div class="flex items-center gap-2">
                                <a :href="doc.url" target="_blank" class="flex-1 text-center bg-white border border-slate-200 rounded-xl py-1.5 text-[10px] font-black text-gold-600 hover:bg-gold-50 transition">عرض</a>
                                <button type="button" @click="renameDocument(doc)" class="p-1.5 text-slate-400 hover:text-gold-600 transition" title="إعادة تسمية">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" @click="deleteDocument(doc)" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- المستندات الاختيارية --}}
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
            <template x-for="doc in optionalDocsList" :key="doc.key">
                <div class="border-2 rounded-2xl p-4 transition-all duration-300" :class="doc.uploaded ? statusBorderClass(doc.status) : 'border-dashed border-slate-200 bg-slate-50/50'">
                    <template x-if="!doc.uploaded">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-slate-700 font-black text-xs" x-text="doc.label"></span>
                            <span class="text-slate-300 text-lg">+</span>
                            <input type="file" class="hidden" :accept="doc.accept" @change="uploadCategoryDocument(doc.key, doc.label, $event)">
                        </label>
                    </template>
                    <template x-if="doc.uploaded">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-700 font-black text-xs" x-text="doc.label"></span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full" :class="statusBadgeClass(doc.status)" x-text="statusLabel(doc.status)"></span>
                            </div>
                            <p x-show="doc.status === 'rejected' && doc.admin_note" class="text-[10px] text-rose-500 font-bold" x-text="'سبب الرفض: ' + doc.admin_note"></p>
                            <div class="flex items-center gap-2">
                                <a :href="doc.url" target="_blank" class="flex-1 text-center bg-white border border-slate-200 rounded-xl py-1.5 text-[10px] font-black text-gold-600 hover:bg-gold-50 transition">عرض</a>
                                <button type="button" @click="renameDocument(doc)" class="p-1.5 text-slate-400 hover:text-gold-600 transition" title="إعادة تسمية">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" @click="deleteDocument(doc)" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- مستندات إضافية (خارج القائمة) --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-navy-100 rounded-xl flex items-center justify-center text-navy-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h3 class="text-slate-800 font-black">مستندات إضافية</h3>
                <p class="text-slate-400 text-[10px] font-bold">أضف أي مستند آخر بمسمى تختاره بنفسك</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <template x-for="doc in customDocs" :key="doc.id">
                <div class="border-2 rounded-2xl p-4 space-y-2" :class="statusBorderClass(doc.status)">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-700 font-black text-xs" x-text="doc.label"></span>
                        <span class="text-[9px] font-black px-2 py-0.5 rounded-full" :class="statusBadgeClass(doc.status)" x-text="statusLabel(doc.status)"></span>
                    </div>
                    <p x-show="doc.status === 'rejected' && doc.admin_note" class="text-[10px] text-rose-500 font-bold" x-text="'سبب الرفض: ' + doc.admin_note"></p>
                    <div class="flex items-center gap-2">
                        <a :href="doc.url" target="_blank" class="flex-1 text-center bg-white border border-slate-200 rounded-xl py-1.5 text-[10px] font-black text-gold-600 hover:bg-gold-50 transition">عرض</a>
                        <button type="button" @click="renameDocument(doc)" class="p-1.5 text-slate-400 hover:text-gold-600 transition" title="إعادة تسمية">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button type="button" @click="deleteDocument(doc)" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="حذف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch gap-3 border-t border-slate-100 pt-6">
            <input type="text" x-model="newCustomLabel" placeholder="اسم المستند (مثال: شهادة عمل)" class="flex-1 bg-slate-50 border-0 rounded-2xl p-3 text-slate-700 font-bold focus:ring-2 focus:ring-gold-500">
            <input type="file" x-ref="customDocInput" class="flex-1 bg-slate-50 border-0 rounded-2xl p-3 text-slate-700 font-bold text-xs" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
            <button type="button" @click="uploadCustomDocument()" class="bg-navy-900 text-white px-6 py-3 rounded-2xl font-black text-sm hover:bg-navy-800 transition flex-shrink-0">إضافة</button>
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
                        <div class="relative w-32 h-32 mx-auto mb-10 group">
                            <div id="avatarDisplay" class="w-full h-full">
                            @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg">
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
        avatarCropper: null,
        avatarFile: null,
        avatarPreview: null,
        languages: {!! json_encode(
            collect($user->languages ?? [['name' => 'العربية', 'level' => 'لغة الأم']])
                ->map(fn($lang) => ['name' => $lang['name'] ?? '', 'level' => $lang['level'] ?? ($lang['cert'] ?? '')])
                ->values()
        ) !!},

        addLanguage() {
            this.languages.push({ name: '', level: '' });
        },

        removeLanguage(index) {
            this.languages.splice(index, 1);
        },

        requiredCategories: [
            { key: 'passport', label: 'جواز السفر', icon: '🌍', accept: '.pdf,.jpg,.jpeg,.png' },
            { key: 'national_id', label: 'الهوية الشخصية', icon: '🆔', accept: '.pdf,.jpg,.jpeg,.png' },
            { key: 'high_school_cert', label: 'شهادة الثانوية', icon: '🎓', accept: '.pdf,.jpg,.jpeg,.png' },
            { key: 'birth_cert', label: 'شهادة الميلاد', icon: '👶', accept: '.pdf,.jpg,.jpeg,.png' },
            { key: 'cv', label: 'السيرة الذاتية (CV)', icon: '📄', accept: '.pdf,.doc,.docx' },
        ],
        optionalCategories: [
            { key: 'language_cert', label: 'شهادة لغة', accept: '.pdf,.jpg,.jpeg,.png' },
            { key: 'courses_cert', label: 'شهادة دورات', accept: '.pdf,.jpg,.jpeg,.png' },
            { key: 'recommendation', label: 'خطاب توصية', accept: '.pdf,.doc,.docx' },
            { key: 'intent_letter', label: 'خطاب نية', accept: '.pdf,.doc,.docx' },
        ],
        documents: {!! json_encode($documents->map(fn($d) => [
            'id' => $d->id,
            'category' => $d->category,
            'label' => $d->label,
            'status' => $d->status,
            'admin_note' => $d->admin_note,
            'url' => $d->url,
        ])) !!},
        newCustomLabel: '',

        get requiredDocsList() {
            return this.requiredCategories.map(c => {
                const match = this.documents.find(d => d.category === c.key);
                return match ? { ...c, ...match, uploaded: true } : { ...c, uploaded: false };
            });
        },

        get optionalDocsList() {
            return this.optionalCategories.map(c => {
                const match = this.documents.find(d => d.category === c.key);
                return match ? { ...c, ...match, uploaded: true } : { ...c, uploaded: false };
            });
        },

        get customDocs() {
            return this.documents.filter(d => !d.category);
        },

        statusLabel(status) {
            return { pending: 'قيد المراجعة', approved: 'مقبول', rejected: 'مرفوض' }[status] || 'قيد المراجعة';
        },

        statusBadgeClass(status) {
            return {
                pending: 'bg-amber-100 text-amber-700',
                approved: 'bg-emerald-100 text-emerald-700',
                rejected: 'bg-rose-100 text-rose-700',
            }[status] || 'bg-amber-100 text-amber-700';
        },

        statusBorderClass(status) {
            return {
                pending: 'border-amber-300 bg-amber-50/30',
                approved: 'border-emerald-400 bg-emerald-50/30',
                rejected: 'border-rose-300 bg-rose-50/30',
            }[status] || 'border-slate-200';
        },

        async uploadCategoryDocument(category, label, event) {
            const file = event.target.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('label', label);
            formData.append('category', category);
            formData.append('file', file);
            await this.submitDocumentForm('{{ route('dashboard.documents.store') }}', formData);
        },

        async uploadCustomDocument() {
            const file = this.$refs.customDocInput.files[0];
            if (!this.newCustomLabel || !file) {
                this.showToast('error', 'الرجاء إدخال اسم المستند واختيار الملف');
                return;
            }
            const formData = new FormData();
            formData.append('label', this.newCustomLabel);
            formData.append('file', file);
            await this.submitDocumentForm('{{ route('dashboard.documents.store') }}', formData);
        },

        async renameDocument(doc) {
            const newLabel = prompt('الاسم الجديد للمستند:', doc.label);
            if (!newLabel || newLabel === doc.label) return;
            const formData = new FormData();
            formData.append('label', newLabel);
            formData.append('_method', 'PATCH');
            await this.submitDocumentForm('{{ url('/dashboard/documents') }}/' + doc.id, formData);
        },

        async deleteDocument(doc) {
            if (!confirm('هل أنت متأكد من حذف هذا المستند؟')) return;
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            await this.submitDocumentForm('{{ url('/dashboard/documents') }}/' + doc.id, formData);
        },

        async submitDocumentForm(url, formData) {
            try {
                await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData,
                });
                window.location.reload();
            } catch (e) {
                this.showToast('error', 'حدث خطأ، حاول مرة أخرى');
            }
        },

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
            const requiredKeys = this.requiredCategories.map(c => c.key);
            boost += this.documents.filter(d => requiredKeys.includes(d.category)).length * 3;
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

    }
}
</script>
@endsection
