@extends('layouts.dashboard')
@section('title', 'شارك تجربتك')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl">
    <div class="max-w-3xl mx-auto">

        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">شارك تجربتك</h1>
            <p class="text-slate-500 font-bold mt-2">قصتك ممكن تكون الدافع لطالب تاني يبدأ رحلته معنا</p>
        </div>

        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl text-sm font-bold flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-rose-500 text-white rounded-2xl text-sm font-bold flex items-center gap-2">
            ⚠️ {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-100">

            @if($testimonial)
                @php
                    $statusMap = [
                        'pending' => ['bg-amber-50 text-amber-600', 'قيد المراجعة ⏳'],
                        'approved' => ['bg-emerald-50 text-emerald-600', 'تمت الموافقة ✅'],
                        'rejected' => ['bg-rose-50 text-rose-600', 'مرفوضة ❌'],
                    ];
                    $current = $statusMap[$testimonial->status] ?? $statusMap['pending'];
                @endphp

                <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
                    <span class="px-4 py-2 rounded-full text-xs font-black {{ $current[0] }}">{{ $current[1] }}</span>
                    @if($testimonial->status === 'approved')
                        <p class="text-xs font-bold text-slate-400">تجربتك ظاهرة الآن في الصفحة الرئيسية 🎉</p>
                    @elseif($testimonial->status === 'pending')
                        <p class="text-xs font-bold text-slate-400">تجربتك تحت مراجعة فريق أوربيت وهتظهر بعد الموافقة عليها</p>
                    @endif
                </div>

                @if($testimonial->status === 'rejected' && $testimonial->admin_note)
                <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 mb-6">
                    <p class="text-xs font-black text-rose-700 mb-1">سبب الرفض:</p>
                    <p class="text-sm font-bold text-rose-600">{{ $testimonial->admin_note }}</p>
                </div>
                @endif

                <form action="{{ route('dashboard.testimonial.update') }}" method="POST" x-data="{ rating: {{ $testimonial->rating }} }">
                    @csrf
                    @method('PUT')

                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">تقييمك للتجربة</label>
                    <div class="flex gap-2 mb-6">
                        <template x-for="i in 5" :key="i">
                            <button type="button" @click="rating = i" class="text-3xl transition-transform hover:scale-110" :class="i <= rating ? 'text-amber-400' : 'text-slate-200'">★</button>
                        </template>
                    </div>
                    <input type="hidden" name="rating" x-model="rating">

                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">تجربتك</label>
                    <textarea name="content" rows="6" required maxlength="2000"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition mb-6"
                        placeholder="احكيلنا كيف كانت رحلتك معنا...">{{ old('content', $testimonial->content) }}</textarea>

                    <button type="submit" class="bg-gold-600 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-gold-700 transition-all shadow-lg shadow-gold-100">
                        حفظ التعديلات
                    </button>
                    <p class="text-[11px] font-bold text-slate-400 mt-3">أي تعديل على التجربة بيرجعها لقيد المراجعة قبل ما تظهر بالموقع من جديد.</p>
                </form>

            @else
                <form action="{{ route('dashboard.testimonial.store') }}" method="POST" x-data="{ rating: 5 }">
                    @csrf

                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">تقييمك للتجربة</label>
                    <div class="flex gap-2 mb-6">
                        <template x-for="i in 5" :key="i">
                            <button type="button" @click="rating = i" class="text-3xl transition-transform hover:scale-110" :class="i <= rating ? 'text-amber-400' : 'text-slate-200'">★</button>
                        </template>
                    </div>
                    <input type="hidden" name="rating" x-model="rating">

                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">احكيلنا تجربتك</label>
                    <textarea name="content" rows="6" required maxlength="2000"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition mb-6"
                        placeholder="احكيلنا كيف ساعدتك أوربيت في رحلتك للحصول على المنحة...">{{ old('content') }}</textarea>

                    <button type="submit" class="bg-gold-600 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-gold-700 transition-all shadow-lg shadow-gold-100">
                        إرسال تجربتي
                    </button>
                    <p class="text-[11px] font-bold text-slate-400 mt-3">تجربتك هتتراجع من فريق أوربيت قبل ما تظهر بالصفحة الرئيسية.</p>
                </form>
            @endif

        </div>
    </div>
</div>
@endsection
