@extends('layouts.dashboard')

@section('title', 'الدفع للمنحة')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10">
    <div class="max-w-6xl mx-auto">

        <div class="flex justify-end mb-6">
            <a href="{{ route('dashboard.scholarships.show', $scholarship->id) }}" class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 font-bold transition">
                <span>العودة لتفاصيل المنحة</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50 mb-6">
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 mb-2">
                تفعيل التقديم على المنحة
            </h1>
            <p class="text-slate-400 font-bold text-sm">
                المنحة: <span class="text-slate-700">{{ $scholarship->title_ar }}</span>
                @unless(config('app.free_mode'))
                    • القيمة: <span class="text-indigo-600">₪{{ number_format($scholarship->price, 2) }}</span>
                @endunless
            </p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500 text-white p-4 rounded-2xl font-black text-sm shadow-lg shadow-emerald-100 flex items-center gap-3 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-500 text-white p-4 rounded-2xl font-black text-sm shadow-lg shadow-rose-100 flex items-center gap-3 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v4m0 4h.01"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ================= FREE MODE / Paywall ================= --}}
        @if(config('app.free_mode'))
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 mb-6 text-right">
                <h3 class="text-slate-800 font-black text-lg mb-2 flex items-center gap-2">
                    <span>🎉</span>
                    <span>هذه المنحة متاحة مجاناً لفترة محدودة!</span>
                </h3>
                <p class="text-slate-400 font-bold text-sm mb-6">
                    يمكنك الآن تفعيل الوصول المجاني لتكملة التقديم بدون دفع أي مبالغ.
                </p>

                <form action="{{ route('dashboard.scholarships.pay', $scholarship->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3 justify-end">
                    @csrf
                    <a href="{{ route('dashboard.scholarships.show', $scholarship->id) }}" class="bg-white border border-slate-200 text-slate-600 px-8 py-4 rounded-2xl font-black hover:bg-slate-50 transition text-center">
                        إلغاء
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                        تفعيل الوصول المجاني
                    </button>
                </form>
            </div>
        @else
        {{-- ================= قسم حسابات الدفع المعتمدة للمنصة ================= --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 mb-6 text-right">

            <h3 class="text-slate-800 font-black text-lg mb-2 flex items-center justify-start gap-2">
                <span>الحسابات والمحافظ المعتمدة للتحويل</span>
                <span class="text-xl">💳</span>
            </h3>
            <p class="text-xs font-bold text-slate-400 mb-6">يرجى تحويل المبلغ المطلوب إلى أحد الحسابات التالية، ثم تعبئة نموذج تأكيد الدفع بالأسفل.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               @php
    $gateways = $setting->payment_gateways ?? [];
    
    // فلترة مرنة تقرأ النص أو البوليان لضمان جلب البوابات المفعلة
    $activeGateways = array_filter($gateways, function($g) {
        $isActive = $g['active'] ?? false;
        return $isActive === true || $isActive === 1 || $isActive === '1' || $isActive === 'on' || $isActive === 'true';
    });
@endphp

                @forelse($activeGateways as $gateway)
                    <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/50 flex items-center justify-between gap-4">
                        {{-- زر النسخ السريع لإراحة المستخدم --}}
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $gateway['account_number'] }}'); alert('تم نسخ رقم الحساب بنجاح!')" 
                                class="p-2 bg-white text-indigo-600 hover:bg-indigo-50 border border-slate-100 rounded-xl transition-all shadow-sm group">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                        </button>
                        
                        <div class="text-right">
                            <span class="text-[10px] font-black text-indigo-600 uppercase bg-indigo-50 px-2 py-0.5 rounded-md mb-1 inline-block">
                                {{ $gateway['name'] }}
                            </span>
                            <p class="text-base font-black text-slate-800 select-all font-mono tracking-wide">
                                {{ $gateway['account_number'] }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-6 border border-dashed border-slate-200 rounded-2xl text-center">
                        <p class="text-slate-400 font-bold text-sm">لم يتم تفعيل أي بوابات دفع حالياً، يرجى التواصل مع الدعم الفني.</p>
                    </div>
                @endforelse
            </div>
        </div>
        {{-- ================================================================= --}}
        @endif

        <form action="{{ route('dashboard.scholarships.pay', $scholarship->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm text-right">
                    <h3 class="text-slate-800 font-black text-lg mb-4">نموذج الدفع</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-slate-500 font-bold mb-2 text-sm">طريقة الدفع المستخدمة</label>
                            <select name="payment_method" class="w-full bg-slate-50 border border-slate-100 focus:border-indigo-300 outline-none rounded-2xl p-3.5 font-bold text-slate-700 transition-all">
                                @foreach($activeGateways as $gateway)
                                    <option value="{{ Str::slug($gateway['name']) }}">{{ $gateway['name'] }}</option>
                                @endforeach
                                <option value="cash">نقدي</option>
                                <option value="other">أخرى</option>
                            </select>
                            @error('payment_method')
                                <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-500 font-bold mb-2 text-sm">رقم الإشعار / رقم العملية</label>
                            <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="أدخل رقم التحويل المستلم" class="w-full bg-slate-50 border border-slate-100 focus:border-indigo-300 outline-none rounded-2xl p-3.5 font-bold text-slate-700 transition-all">
                            @error('transaction_id')
                                <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm text-right">
                    <h3 class="text-slate-800 font-black text-lg mb-4">بيانات التحويل</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-slate-500 font-bold mb-2 text-sm">اسم البنك / المحفظة</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full bg-slate-50 border border-slate-100 focus:border-indigo-300 outline-none rounded-2xl p-3.5 font-bold text-slate-700 transition-all" placeholder="مثال: بنك فلسطين أو محفظة كاش">
                            @error('bank_name')
                                <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-500 font-bold mb-2 text-sm">اسم المحوّل الكامل</label>
                            <input type="text" name="transfer_from" value="{{ old('transfer_from') }}" class="w-full bg-slate-50 border border-slate-100 focus:border-indigo-300 outline-none rounded-2xl p-3.5 font-bold text-slate-700 transition-all" placeholder="الاسم الذي قمت بالتحويل من خلاله">
                            @error('transfer_from')
                                <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-500 font-bold mb-2 text-sm">رفع صورة الإيصال</label>
                            <input type="file" name="receipt_image" accept="image/*" class="w-full bg-slate-50 border border-slate-100 focus:border-indigo-300 outline-none rounded-2xl p-2.5 font-bold text-slate-500 transition-all">
                            @error('receipt_image')
                                <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm text-right">
                <h3 class="text-slate-800 font-black text-lg mb-4">ملاحظات إضافية (اختياري)</h3>
                <textarea name="admin_notes" rows="4" class="w-full bg-slate-50 border border-slate-100 focus:border-indigo-300 outline-none rounded-2xl p-3.5 font-bold text-slate-700 transition-all" placeholder="أي تفاصيل أخرى تود إعلام الإدارة بها..."></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('dashboard.scholarships.show', $scholarship->id) }}" class="bg-white border border-slate-200 text-slate-600 px-8 py-4 rounded-2xl font-black hover:bg-slate-50 transition text-center">
                    إلغاء
                </a>
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    إرسال بيانات الدفع للأدمن
                </button>
            </div>
        </form>

        <p class="text-xs text-slate-400 font-bold mt-6 text-center">
            بعد الإرسال سيتم مراجعة إيصال التحويل وتفعيل الطلب من قِبل الإدارة.
        </p>

    </div>
</div>
@endsection