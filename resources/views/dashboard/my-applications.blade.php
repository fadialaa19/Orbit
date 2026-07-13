@extends('layouts.dashboard')

@section('title', 'طلباتي')

@section('header_search', '')

@section('content')
    {{-- page name: طلباتي / My Applications (dynamic from orders table)
         This file must fetch real data from DB, not placeholders. --}}

    @php
        $applications = auth()->user()->orders()->with('scholarship')->latest()->get();
        $counts = [
            'pending' => $applications->where('status', 'pending')->count(),
            'paid' => $applications->where('status', 'paid')->count(),
            'failed' => $applications->where('status', 'failed')->count(),
        ];
    @endphp

    <div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10" dir="rtl">
        <div class="max-w-5xl mx-auto">

            <div class="mb-10 text-right">
                <h1 class="text-3xl font-black text-slate-800 mb-2">تتبع الطلبات</h1>
                <p class="text-slate-400 font-bold italic">راقب حالة طلبات المنح الدراسية الخاصة بك</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                @foreach([
                    ['إجمالي الطلبات', $applications->count(), '📄', 'text-gold-600'],
                    ['قيد المراجعة', $counts['pending'], '🕒', 'text-gold-600'],
                    ['مقبولة', $counts['paid'], '✅', 'text-green-600'],
                    ['مرفوضة', $counts['failed'], '❌', 'text-red-600'],
                ] as $stat)
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 flex flex-col items-center">
                        <span class="text-2xl mb-2">{{ $stat[2] }}</span>
                        <span class="text-2xl font-black {{ $stat[3] }}">{{ $stat[1] }}</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $stat[0] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="space-y-6">
                @forelse($applications as $order)
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                            <div class="text-right">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-black text-slate-800">{{ $order->scholarship->title_ar ?? '—' }}</h3>
                                    @if($order->status === 'pending')
                                        <span class="bg-gold-100 text-gold-700 px-3 py-1 rounded-lg text-[10px] font-black italic">قيد المراجعة</span>
                                    @elseif($order->status === 'paid')
                                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-lg text-[10px] font-black italic">مقبولة</span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-lg text-[10px] font-black italic">مرفوضة</span>
                                    @endif
                                </div>

                                <p class="text-slate-400 font-bold text-xs">تم التقديم في: {{ $order->created_at->format('d M Y') }}</p>
                                @unless(config('app.free_mode'))
                                    <p class="text-slate-500 font-bold text-[10px] mt-1">المبلغ: ₪{{ number_format($order->amount, 2) }}</p>
                                @endunless
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('dashboard.scholarships.show', $order->scholarship_id) }}" class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-xl text-xs font-black hover:bg-slate-50 transition">
                                    عرض المنحة
                                </a>

                                @if($order->status === 'paid')
                                    <a href="{{ route('dashboard.scholarships.show', $order->scholarship_id) }}" class="bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-black shadow-lg shadow-green-100 hover:bg-green-700 transition">
                                        استمرار التقديم
                                    </a>
                                @endif
                            </div>
                        </div>

                        @unless(config('app.free_mode'))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-bold">رقم العملية</p>
                                    <p class="text-sm font-black text-slate-700">{{ $order->transaction_id ?? '—' }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-bold">البنك / المحوّل</p>
                                    <p class="text-sm font-black text-slate-700">{{ $order->bank_name ?? '—' }} • {{ $order->transfer_from ?? '—' }}</p>
                                </div>
                            </div>

                            @if($order->receipt_image)
                                <div class="mt-4">
                                    <a href="{{ \Storage::disk('public')->url($order->receipt_image) }}" target="_blank" class="text-[10px] font-black text-gold-600 hover:underline">عرض الإيصال ↗</a>
                                </div>
                            @endif
                        @endunless
                    </div>
                @empty
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-50 text-center">
                        <p class="text-slate-400 font-bold">لا توجد طلبات دفع بعد</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection


