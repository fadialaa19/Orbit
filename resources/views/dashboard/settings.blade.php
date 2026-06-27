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
                            ['orders', 'طلبات الدفع'],
                            ['privacy', 'الخصوصية'],
                            ['notifications', 'الإشعارات'],
                            ['language', 'اللغة والمنطقة']
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
                        <div class="p-8">

                            <div class="text-center mb-10 lg:mb-0 lg:text-right">
                                <div class="relative w-28 h-28 mx-auto lg:mx-0 mb-6">
                                    <div class="w-full h-full rounded-full bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center text-white text-4xl font-black shadow-2xl">أ</div>
                                    <label class="absolute -bottom-2 -right-2 bg-indigo-600 text-white p-3 rounded-full shadow-lg cursor-pointer hover:bg-indigo-700 transition block w-12 h-12 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                        <input type="file" class="hidden">
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">الاسم الكامل</label>
                                    <input type="text" value="أحمد محمد علي" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500 text-lg">
                                </div>
                                <div>
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">البريد الإلكتروني</label>
                                    <input type="email" value="ahmed@example.com" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-slate-500 font-bold mb-3 text-sm">كلمة المرور الجديدة</label>
                                    <input type="password" placeholder="اترك فارغاً لعدم التغيير" class="w-full bg-slate-50 border-0 rounded-2xl p-5 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <button class="bg-indigo-600 text-white px-12 py-4 rounded-2xl font-black shadow-lg hover:bg-indigo-700 transition w-full md:w-auto">حفظ التغييرات</button>
                        </div>
                    </div>

                    <!-- Privacy Tab -->
                    <div x-show="tab === 'privacy'" x-transition class="p-8">
                        <div class="space-y-6">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-black text-slate-800">جعل ملفي مرئياً للمنح</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-black text-slate-800">تلقي رسائل من الجامعات</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button class="mt-8 bg-green-600 text-white px-12 py-4 rounded-2xl font-black shadow-lg hover:bg-green-700 transition w-full">حفظ إعدادات الخصوصية</button>
                    </div>



                    <!-- Notifications Tab -->
                    <div x-show="tab === 'notifications'" x-transition class="p-8">

                        <h3 class="font-black text-slate-800 mb-6 text-lg">تفضيلات الإشعارات</h3>
                        <div class="space-y-4 mb-8">
                            <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-slate-100 transition">
                                <div class="w-5 h-5 border-2 border-slate-200 rounded-sm flex items-center justify-center {{ true ? 'bg-indigo-600 border-indigo-600' : '' }}">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="font-bold text-slate-700">إشعارات المنح الجديدة</span>
                            </label>
                            <!-- More toggles... -->
                        </div>
                        <button class="bg-indigo-600 text-white px-12 py-4 rounded-2xl font-black shadow-lg hover:bg-indigo-700">حفظ</button>
                    </div>

                    <!-- Language Tab -->
                    <div x-show="tab === 'language'" x-transition class="p-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-slate-500 font-bold mb-3 text-sm">اللغة المفضلة</label>
                                <select class="w-full bg-slate-50 border-0 rounded-2xl p-5 font-bold">
                                    <option>العربية</option>
                                    <option>English</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-500 font-bold mb-3 text-sm">المنطقة الزمنية</label>
                                <select class="w-full bg-slate-50 border-0 rounded-2xl p-5 font-bold">
                                    <option>(GMT+3) الرياض</option>
                                </select>
                            </div>
                        </div>
                        <button class="mt-6 bg-indigo-600 text-white w-full py-4 rounded-2xl font-black shadow-lg hover:bg-indigo-700">حفظ اللغة</button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

