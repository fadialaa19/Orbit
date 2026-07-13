@extends('layouts.dashboard')

@section('title', 'طلبات الدفع')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10" dir="rtl">
    <div class="max-w-6xl mx-auto">

        <div class="flex items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-800">طلبات الدفع</h1>
                <p class="text-slate-400 text-sm font-bold">تتبع حالة طلبات التقديم عن طريقنا</p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 overflow-hidden">
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-full bg-amber-50 text-amber-700 text-xs font-black">قيد المراجعة</span>
                    <span class="text-xs font-bold text-slate-500">{{ $ordersByStatus['pending']->count() }} طلب</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-full bg-emerald-50 text-emerald-700 text-xs font-black">مدفوع</span>
                    <span class="text-xs font-bold text-slate-500">{{ $ordersByStatus['paid']->count() }} طلب</span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @foreach(['pending' => 'قيد المراجعة', 'paid' => 'مدفوع', 'failed' => 'مرجوع/فشل'] as $key => $label)
                        @php($list = $ordersByStatus[$key] ?? collect())
                        <div class="border border-slate-100 rounded-[1.5rem] p-4 bg-white">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-black text-slate-800">{{ $label }}</span>
                                <span class="text-xs font-bold text-slate-400">{{ $list->count() }}</span>
                            </div>

                            @if($list->isEmpty())
                                <p class="text-slate-400 text-sm font-bold">لا يوجد</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($list as $order)
                                        <div class="p-3 rounded-2xl bg-slate-50/60 border border-slate-100">
                                            <p class="text-xs font-black text-gold-600">
                                                {{ $order->scholarship->title_ar ?? '—' }}
                                            </p>
                                            <p class="text-[11px] font-bold text-slate-600">قيمة: ₪{{ number_format($order->amount, 2) }}</p>
                                            <p class="text-[10px] font-bold text-slate-400">{{ $order->created_at->format('Y-m-d') }}</p>

                                            @if($order->receipt_image)
                                                <a href="{{ \Storage::disk('public')->url($order->receipt_image) }}" target="_blank" class="text-[10px] font-black text-gold-600 hover:underline mt-2 inline-block">عرض الإيصال ↗</a>
                                            @endif

                                            @if($order->status === 'paid')
                                                <a href="{{ route('dashboard.scholarships.show', $order->scholarship_id) }}" class="text-[10px] font-black text-emerald-700 hover:underline mt-2 inline-block">العودة للمنحة</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

