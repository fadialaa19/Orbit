@extends('layouts.dashboard')
@section('title', 'الإعدادات')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10" dir="rtl" x-data="{ tab: 'account' }">
    <div class="max-w-6xl mx-auto">
        
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50 mb-8">
            <h1 class="text-2xl font-black text-indigo-600 mb-2">الإعدادات</h1>
            <p class="text-slate-400 text-sm font-bold">إدارة حسابك والتفضيلات الخاصة بك</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Tabs -->
            <div class="w-full lg:w-64 order-2 lg:order-1">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 p-2">
                    <nav class="space-y-1">
                        @foreach([
                            ['account', 'الحساب'],
                            ['privacy', 'الخصوصية'],
                            ['notifications', 'الإشعارات'],
                        ] as $item)

                        <button @click="tab = '{{ $item[0] }}'" 
                            :class="tab === '{{ $item[0] }}' ? 'bg-indigo-50 text-indigo-600 border-r-4 border-indigo-500 font-black shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="w-full text-right py-4 px-6 rounded-2xl font-bold text-sm transition-all duration-200">
                            {{ $item[1] }}
                        </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 order-1 lg:order-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 overflow-hidden">
                    
                    <!-- Account Tab -->
                    <div x-show="tab === 'account'" x-transition>
                        <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-8">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_type" value="profile">

                            @if(session('success'))
                                <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 p-4 rounded-2xl font-bold text-sm mb-6">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="bg-rose-50 text-rose-700 border border-rose-100 p-4 rounded-2xl font-bold text-sm mb-6">{{ session('error') }}</div>
                            @endif

                            <div class="text-center mb-10 lg:mb-0 lg:text-right">
                                <div class="relative w-28 h-28 mx-auto lg:mx-0 mb-6">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full rounded-full object-cover shadow-2xl">
                                    @else
                                        <div class="w-full h-full rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white text-4xl font-black shadow-2xl">{{ mb_substr($user->name, 0, 1) }}</div>
                                    @endif
                                    <label class="absolute -bottom-2 -right-2 bg-indigo-600 text-white p-3 rounded-full shadow-lg cursor-pointer hover:bg-indigo-700 transition block w-12 h-12 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                                    </label>
                                </div>
                                @error('avatar')
                                    <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">الاسم الكامل</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500 text-lg">
                                    @error('name')
                                        <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">البريد الإلكتروني</label>
                                    <input type="email" value="{{ $user->email }}" disabled class="w-full bg-slate-100 border-0 rounded-2xl p-5 text-slate-400 font-bold cursor-not-allowed">
                                    <p class="text-[10px] text-slate-400 font-bold mt-2">لتغيير بريدك الإلكتروني تواصل مع الدعم</p>
                                </div>
                                <div>
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">كلمة المرور الحالية</label>
                                    <input type="password" name="current_password" placeholder="مطلوبة فقط عند تغيير كلمة المرور" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500">
                                    @error('current_password')
                                        <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">كلمة المرور الجديدة</label>
                                    <input type="password" name="new_password" placeholder="اترك فارغاً لعدم التغيير" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500">
                                    @error('new_password')
                                        <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">تأكيد كلمة المرور الجديدة</label>
                                    <input type="password" name="new_password_confirmation" placeholder="أعد كتابة كلمة المرور الجديدة" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <button type="submit" class="bg-indigo-600 text-white px-12 py-4 rounded-2xl font-black shadow-lg hover:bg-indigo-700 transition w-full md:w-auto">حفظ التغييرات</button>
                        </form>
                    </div>

                    <!-- Privacy Tab -->
                    <div x-show="tab === 'privacy'" x-transition class="p-8">
                        <form action="{{ route('dashboard.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_type" value="privacy">

                            @if(session('success'))
                                <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 p-4 rounded-2xl font-bold text-sm mb-6">{{ session('success') }}</div>
                            @endif

                            <div class="space-y-6">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="font-black text-slate-800">جعل ملفي مرئياً للمنح</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="profile_visible_to_scholarships" value="1" {{ ($user->preferences['profile_visible_to_scholarships'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="font-black text-slate-800">تلقي رسائل من الجامعات</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="receive_university_messages" value="1" {{ ($user->preferences['receive_university_messages'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="mt-8 bg-green-600 text-white px-12 py-4 rounded-2xl font-black shadow-lg hover:bg-green-700 transition w-full">حفظ إعدادات الخصوصية</button>
                        </form>
                    </div>

                    <!-- Notifications Tab -->
                    <div x-show="tab === 'notifications'" x-transition class="p-8">
                        <form action="{{ route('dashboard.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_type" value="notifications">

                            @if(session('success'))
                                <div class="bg-emerald-50 text-emerald-700 border border-emerald-100 p-4 rounded-2xl font-bold text-sm mb-6">{{ session('success') }}</div>
                            @endif

                            <h3 class="font-black text-slate-800 mb-6 text-lg">تفضيلات الإشعارات</h3>
                            <div class="space-y-4 mb-8">
                                <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-slate-100 transition">
                                    <input type="checkbox" name="notify_new_scholarships" value="1" {{ ($user->preferences['notify_new_scholarships'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded-sm border-2 border-slate-200 text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-slate-700">إشعارات المنح الجديدة</span>
                                </label>
                            </div>
                            <button type="submit" class="bg-indigo-600 text-white px-12 py-4 rounded-2xl font-black shadow-lg hover:bg-indigo-700">حفظ</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

