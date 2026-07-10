@extends('layouts.admin')

@section('title', 'الإشعارات')
@section('breadcrumb', 'كل الإشعارات')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-800">كل الإشعارات</h2>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($notifications as $n)
                <a href="{{ $n->data['link'] ?? '#' }}" class="block p-5 hover:bg-slate-50/80 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $n->data['title'] ?? 'إشعار' }}</p>
                            <p class="text-xs font-bold text-slate-400 mt-1">{{ $n->data['body'] ?? '' }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$n->read_at)
                                <span class="w-2 h-2 bg-gold-600 rounded-full"></span>
                            @endif
                            <span class="text-[10px] text-slate-400 font-bold whitespace-nowrap">{{ $n->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-10 text-center text-slate-400 font-bold text-sm">لا توجد إشعارات حالياً</div>
            @endforelse
        </div>
    </div>

    {{ $notifications->links() }}
</div>
@endsection
