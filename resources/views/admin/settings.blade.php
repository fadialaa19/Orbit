@extends('layouts.admin')
@section('title', 'إعدادات المنصة')
@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-900">إعدادات المنصة</h1>
            <p class="text-sm font-bold text-slate-400 mt-1">إدارة بوابات الدفع، المساعد الذكي، وحالة الموقع</p>
        </div>
        <button onclick="document.getElementById('settings-form').submit()" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-black shadow-lg hover:bg-indigo-700 transition-all flex items-center gap-2">
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
            <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
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
                    <input type="password" name="ai_api_key" value="{{ $setting->ai_api_key }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-mono outline-none focus:border-emerald-500">
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
    </form>
</div>
@endsection