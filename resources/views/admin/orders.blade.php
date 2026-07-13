@extends('layouts.admin')

@section('title', 'طلبات الدفع')
@section('breadcrumb', 'طلبات الدفع والتحقق')

@section('content')
<div class="max-w-full mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-500 text-white p-4 rounded-2xl font-black text-sm shadow-lg shadow-emerald-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">طلبات الدفع</h1>
            <p class="text-xs font-bold text-slate-400 mt-1">التحقق من عمليات التحويل البنكي المحلية</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm border-r-4 border-r-gold-500">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">الإيرادات الكلية</p>
            <span class="text-2xl font-black text-gold-600">₪{{ number_format($stats['total_revenue'] ?? 0, 2) }}</span>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm border-r-4 border-r-amber-500">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">قيد المراجعة</p>
            <span class="text-2xl font-black text-amber-600">{{ $stats['pending_count'] ?? 0 }}</span>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm border-r-4 border-r-emerald-500">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">مدفوعة</p>
            <span class="text-2xl font-black text-emerald-600">{{ $stats['paid_count'] ?? 0 }}</span>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm border-r-4 border-r-rose-500">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">مرفوضة</p>
            <span class="text-2xl font-black text-rose-600">{{ $stats['failed_count'] ?? 0 }}</span>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50/30">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">الطالب والمنحة</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">البنك والإيصال</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider text-left">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800 text-sm">{{ $order->user->name ?? '—' }}</p>
                            <p class="text-[10px] font-bold text-gold-600 uppercase tracking-tight">{{ $order->scholarship->title_ar ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-lg font-black text-slate-800">₪{{ number_format($order->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-slate-600">{{ $order->bank_name ?? '—' }}</p>
                            <p class="text-[10px] font-bold text-slate-400">{{ $order->transfer_from ?? '—' }}</p>
                            @if($order->receipt_image)
                                <a href="{{ \Storage::disk('public')->url($order->receipt_image) }}" target="_blank" class="text-[10px] font-black text-gold-600 hover:underline mt-1 inline-block">عرض الإيصال ↗</a>
                            @else
                                <span class="text-[10px] font-bold text-slate-300">لا يوجد إيصال</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusStyles = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                    'failed' => 'bg-rose-100 text-rose-700',
                                    'refunded' => 'bg-slate-100 text-slate-700',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد المراجعة',
                                    'paid' => 'مدفوع',
                                    'failed' => 'مرفوض',
                                    'refunded' => 'مسترد',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-500">{{ $order->created_at->format('Y-m-d') }}</span>
                            <p class="text-[10px] text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4 text-left">
                            @if($order->status === 'pending')
                                <div class="flex items-center gap-1 justify-end">
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="p-2 text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl transition" title="تأكيد الدفع">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition" title="رفض">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <span class="text-5xl block mb-4">📋</span>
                            <p class="text-slate-400 font-bold">لا توجد طلبات دفع حالياً</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-50">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

