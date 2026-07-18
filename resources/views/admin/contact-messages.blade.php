@extends('layouts.admin')

@section('title', 'رسائل اتصل بنا')
@section('breadcrumb', 'رسائل اتصل بنا')

@section('content')
<div x-data="{ openId: null }" class="max-w-full mx-auto space-y-8">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] min-w-[300px] p-4 bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-2xl flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">رسائل اتصل بنا</h1>
            <p class="text-xs font-bold text-slate-400 mt-1">رسائل من صفحة "تواصل معنا" العامة بالموقع</p>
        </div>
        @if($pendingCount > 0)
            <span class="px-4 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-2xl text-xs font-black">{{ $pendingCount }} رسالة بانتظار المراجعة</span>
        @endif
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-50">
            @forelse($messages as $msg)
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-gold-100 text-gold-700 flex items-center justify-center font-black shrink-0">
                            {{ mb_substr($msg->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-black text-sm text-slate-800">{{ $msg->name }}</p>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-md {{ $msg->status === 'pending' ? 'bg-rose-50 text-rose-600' : ($msg->status === 'read' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}">
                                    {{ ['pending' => 'جديدة', 'read' => 'تمت المراجعة', 'resolved' => 'تم الرد'][$msg->status] }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 font-bold mt-0.5" dir="ltr">{{ $msg->email }}</p>
                            <p class="text-sm font-black text-slate-700 mt-2">{{ $msg->subject }}</p>
                            <p class="text-xs text-slate-500 font-bold mt-1 leading-relaxed" x-show="openId === {{ $msg->id }}" x-cloak>{{ $msg->message }}</p>
                            <button @click="openId = openId === {{ $msg->id }} ? null : {{ $msg->id }}" class="text-[11px] font-black text-gold-600 hover:underline mt-2">
                                <span x-text="openId === {{ $msg->id }} ? 'إخفاء الرسالة' : 'عرض الرسالة كاملة'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="mailto:{{ $msg->email }}?subject=رد: {{ $msg->subject }}" class="px-4 py-2 bg-navy-900 text-white rounded-xl text-xs font-black hover:bg-navy-800 transition">الرد بالإيميل</a>
                        <form action="{{ route('admin.contact-messages.status', $msg) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $msg->status === 'resolved' ? 'pending' : ($msg->status === 'pending' ? 'read' : 'resolved') }}">
                            <button type="submit" class="px-4 py-2 bg-slate-50 border border-slate-100 text-slate-600 rounded-xl text-xs font-black hover:bg-slate-100 transition">
                                {{ $msg->status === 'resolved' ? 'إعادة فتح' : ($msg->status === 'pending' ? 'وضع كمقروءة' : 'وضع كمُنجزة') }}
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-[10px] text-slate-300 font-bold mt-3">{{ $msg->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <div class="p-12 text-center">
                <span class="text-5xl block mb-4">✉️</span>
                <p class="text-slate-400 font-bold">لا توجد رسائل تواصل حتى الآن</p>
            </div>
            @endforelse
        </div>
        <div class="p-4 border-t border-slate-50">{{ $messages->links() }}</div>
    </div>
</div>
@endsection
