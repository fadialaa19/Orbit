@extends('layouts.admin')

@section('title', 'نتائج البحث')
@section('breadcrumb', 'نتائج البحث')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
        <p class="text-slate-500 font-bold text-sm">نتائج البحث عن: <span class="text-gold-600 font-black">{{ $q ?: '—' }}</span></p>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h2 class="text-lg font-black text-slate-800">الطلاب ({{ count($results['students']) }})</h2>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($results['students'] as $student)
                <a href="{{ route('admin.students.index', ['search' => $student->name]) }}" class="flex items-center justify-between p-5 hover:bg-slate-50/80 transition">
                    <div>
                        <p class="text-sm font-black text-slate-800">{{ $student->name }}</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $student->email }}</p>
                    </div>
                    <span class="text-gold-600 font-black text-sm">←</span>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold text-sm">لا توجد نتائج</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h2 class="text-lg font-black text-slate-800">التذاكر ({{ count($results['tickets']) }})</h2>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($results['tickets'] as $ticket)
                <a href="{{ route('admin.tickets.index') }}" class="flex items-center justify-between p-5 hover:bg-slate-50/80 transition">
                    <div>
                        <p class="text-sm font-black text-slate-800">{{ $ticket->subject }}</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $ticket->user->name ?? 'مستخدم' }} • {{ $ticket->status }}</p>
                    </div>
                    <span class="text-gold-600 font-black text-sm">←</span>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold text-sm">لا توجد نتائج</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h2 class="text-lg font-black text-slate-800">المنح الدراسية ({{ count($results['scholarships']) }})</h2>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($results['scholarships'] as $scholarship)
                <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="flex items-center justify-between p-5 hover:bg-slate-50/80 transition">
                    <div>
                        <p class="text-sm font-black text-slate-800">{{ $scholarship->title_ar ?? $scholarship->title_en }}</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $scholarship->country }}</p>
                    </div>
                    <span class="text-gold-600 font-black text-sm">←</span>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold text-sm">لا توجد نتائج</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
