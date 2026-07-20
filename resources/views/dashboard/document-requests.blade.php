@extends('layouts.dashboard')
@section('title', 'طلب الأوراق الرسمية')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl" x-data="{ selected: '' }">
    <div class="max-w-4xl mx-auto">

        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">طلب استخراج الأوراق الرسمية</h1>
            <p class="text-slate-500 font-bold mt-2">فريقنا بيتابع استخراج أوراقك الرسمية من الوزارات الفلسطينية نيابة عنك</p>
        </div>

        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl text-sm font-bold flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-sm font-bold">
            {{ $errors->first() }}
        </div>
        @endif

        {{-- فورم طلب مستند جديد --}}
        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-100 mb-10">
            <form action="{{ route('dashboard.document-requests.submit') }}" method="POST" class="space-y-8">
                @csrf

                <div>
                    <label class="block text-sm font-black text-slate-700 mb-4">اختر المستند اللي بدك تستخرجه</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($documents as $name => $doc)
                        <label class="flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all"
                               :class="selected === @js($name) ? 'border-gold-500 bg-gold-50' : 'border-slate-100 hover:border-slate-200'">
                            <input type="radio" name="document_type" value="{{ $name }}" x-model="selected" required class="mt-1 w-5 h-5 text-gold-600 focus:ring-gold-500">
                            <div class="text-2xl shrink-0">{{ $doc['icon'] }}</div>
                            <div class="flex-1">
                                <p class="font-black text-slate-800 text-sm leading-snug">{{ $name }}</p>
                                <p class="text-[11px] text-slate-400 font-bold mt-1">{{ $doc['source'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">ملاحظات إضافية (اختياري)</label>
                    <textarea name="notes" rows="4" placeholder="أي تفاصيل بتساعدنا نخدمك بشكل أسرع..."
                              class="w-full bg-slate-50 border-2 border-transparent focus:border-gold-500 focus:bg-white rounded-2xl py-4 px-5 outline-none transition-all font-medium resize-none">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" :disabled="!selected"
                        class="w-full bg-gradient-to-r from-gold-600 to-gold-700 text-white py-5 rounded-[2rem] font-black text-lg shadow-xl hover:shadow-2xl hover:scale-[1.01] transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-3">
                    إرسال الطلب
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </button>
            </form>
        </div>

        {{-- طلباتي السابقة --}}
        <div>
            <h2 class="text-xl font-black text-slate-800 mb-5">طلباتي السابقة</h2>

            @forelse($myRequests as $request)
                @php
                    $statusMap = [
                        'pending' => ['bg-amber-50 text-amber-600', 'قيد المراجعة'],
                        'open' => ['bg-blue-50 text-blue-600', 'قيد المتابعة'],
                        'closed' => ['bg-emerald-50 text-emerald-600', 'مكتمل'],
                    ];
                    $current = $statusMap[$request->status] ?? $statusMap['pending'];
                @endphp
                <a href="{{ route('dashboard.tickets.show', $request) }}" class="block bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-gold-100 transition mb-3 flex items-center justify-between gap-4">
                    <div>
                        <p class="font-black text-slate-800 text-sm">{{ str_replace('📄 طلب استخراج مستند: ', '', $request->subject) }}</p>
                        <p class="text-[11px] text-slate-400 font-bold mt-1">{{ $request->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-[11px] font-black shrink-0 {{ $current[0] }}">{{ $current[1] }}</span>
                </a>
            @empty
                <div class="bg-white rounded-2xl p-10 text-center border border-slate-100">
                    <p class="text-slate-400 font-bold text-sm">ما في أي طلبات سابقة، اطلب أول مستند من الفورم فوق</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
