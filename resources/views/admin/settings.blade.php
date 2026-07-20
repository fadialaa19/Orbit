@extends('layouts.admin')
@section('title', 'إعدادات المنصة')
@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-900">إعدادات المنصة</h1>
            <p class="text-sm font-bold text-slate-400 mt-1">إدارة المساعد الذكي وحالة الموقع</p>
        </div>
        <button onclick="document.getElementById('settings-form').submit()" class="bg-gold-600 text-white px-8 py-3 rounded-xl font-black shadow-lg hover:bg-gold-700 transition-all flex items-center gap-2">
            حفظ جميع التغييرات
        </button>
    </div>

    <form id="settings-form" method="POST" action="{{ route('admin.settings.update', $setting->id) }}" class="space-y-6">
        @csrf
        @method('PATCH')

<!-- <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-sm">01</span>
                بوابات الدفع
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
    $gateways = $setting->payment_gateways ?? [
        ['name' => 'فودافون كاش', 'active' => false, 'account_number' => ''], 
        ['name' => 'بنك فلسطين', 'active' => false, 'account_number' => '']
    ];
@endphp

@foreach($gateways as $index => $gateway)
<div class="p-4 border border-slate-100 rounded-2xl bg-slate-50/50 text-right">
    <div class="flex items-center justify-between mb-3">
        <input type="text" name="payment_gateways[{{ $index }}][name]" value="{{ $gateway['name'] }}" class="bg-transparent font-black text-slate-800 outline-none text-right">
        
        <label class="relative inline-flex items-center cursor-pointer">
            {{-- حقل مخفي ذكي: يضمن إرسال قيمة 0 إذا كان الزر غير مفعل، بدلاً من اختفاء الحقل كلياً --}}
            <input type="hidden" name="payment_gateways[{{ $index }}][active]" value="0">
            
            <input type="checkbox" name="payment_gateways[{{ $index }}][active]" value="1" {{ ($gateway['active'] ?? false) ? 'checked' : '' }} class="sr-only peer">
            <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-gold-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
        </label>
    </div>
    <input type="text" name="payment_gateways[{{ $index }}][account_number]" value="{{ $gateway['account_number'] }}" placeholder="رقم الحساب أو المحفظة" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono tracking-wide text-right">
</div>
@endforeach
            </div>
        </div> -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6">
                <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm">02</span>
                    المساعد الذكي (AI)
                </h2>
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-400 tracking-widest uppercase">API KEY</label>
                    <div class="relative" x-data="{ pwShow: false }">
                        <input type="password" :type="pwShow ? 'text' : 'password'" name="ai_api_key" value="{{ $setting->ai_api_key }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 font-mono outline-none focus:border-emerald-500">
                        <button type="button" @click="pwShow = !pwShow" tabindex="-1" class="absolute inset-y-0 left-3 flex items-center text-slate-300 hover:text-emerald-600 transition-colors">
                            <svg x-show="!pwShow" x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="pwShow" x-cloak x-transition:enter="transition duration-500 [transition-timing-function:cubic-bezier(.68,-0.55,.27,1.55)]" x-transition:enter-start="opacity-0 scale-0 -rotate-180" x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 012.132-3.411m3.132-2.507A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.964 9.964 0 01-1.563 3.029M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 text-white rounded-[1.5rem] p-6 shadow-xl border border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-black flex items-center gap-2">
                        <span class="w-8 h-8 bg-rose-500 text-white rounded-lg flex items-center justify-center text-sm">03</span>
                        وضع الصيانة
                    </h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="maintenance_mode" {{ $setting->maintenance_mode ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-700 rounded-full peer peer-checked:bg-rose-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>
                <div class="space-y-4">
                    <input type="text" name="maintenance_message" value="{{ $setting->maintenance_message }}" placeholder="رسالة التحديث..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-300">
                    
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">تاريخ ووقت انتهاء الصيانة</label>
    
    <input type="datetime-local" 
           name="maintenance_until" 
           value="{{ $setting->maintenance_until ? \Carbon\Carbon::parse($setting->maintenance_until)->format('Y-m-d\TH:i') : '' }}"
           class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-300 outline-none focus:border-rose-500">
    
    <p class="text-[10px] text-slate-500 font-medium">سيظهر العداد التنازلي للزوار بناءً على هذا التوقيت.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gold-100 text-gold-600 rounded-lg flex items-center justify-center text-sm">📄</span>
                    خدمة استخراج الأوراق الرسمية
                </h2>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="document_service_enabled" {{ $setting->document_service_enabled ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <p class="text-xs font-bold text-slate-400 -mt-2">
                فعّلها لما يكون مندوب استخراج الأوراق متاح. لو أوقفتها، صفحة "الأوراق الرسمية" للزائر وصفحة الطلب بلوحة الطالب رح تظهر برسالة "الخدمة متوقفة مؤقتاً" بدل نموذج الطلب.
            </p>
        </div>

        <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">04</span>
                بيانات التواصل والروابط الاجتماعية
            </h2>
            <p class="text-xs font-bold text-slate-400 mb-6 -mt-4">بتظهر بفوتر الموقع وصفحة "تواصل معنا" - اتركها فاضية لإخفاء أي رابط ما بدك تعرضه</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">البريد الإلكتروني الظاهر للزوار</label>
                    <input type="email" name="contact_email" value="{{ $setting->contact_email }}" placeholder="orbit.ships@gmail.com" dir="ltr"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-gold-500 transition text-left">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">رقم الهاتف</label>
                    <input type="text" name="contact_phone" value="{{ $setting->contact_phone }}" placeholder="+970 59 270 4945" dir="ltr"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-gold-500 transition text-left">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">📘 رابط فيسبوك</label>
                    <input type="url" name="facebook_url" value="{{ $setting->facebook_url }}" placeholder="https://facebook.com/..." dir="ltr"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-gold-500 transition text-left">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">📸 رابط انستقرام</label>
                    <input type="url" name="instagram_url" value="{{ $setting->instagram_url }}" placeholder="https://instagram.com/..." dir="ltr"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-gold-500 transition text-left">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">💬 رابط واتساب</label>
                    <input type="url" name="whatsapp_url" value="{{ $setting->whatsapp_url }}" placeholder="https://wa.me/970592704945" dir="ltr"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-gold-500 transition text-left">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">✈️ رابط تيليجرام</label>
                    <input type="url" name="telegram_url" value="{{ $setting->telegram_url }}" placeholder="https://t.me/..." dir="ltr"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-gold-500 transition text-left">
                </div>
            </div>
        </div>
    </form>
</div>
@endsection