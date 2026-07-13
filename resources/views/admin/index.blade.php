@extends('layouts.admin')

@section('title', 'نظرة عامة')
@section('breadcrumb', 'نظرة عامة')

@section('content')
<div class="max-w-full mx-auto">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @if(auth()->user()->role === 'super_admin' || in_array('students', auth()->user()->permissions ?? []))
        <div class="group bg-white rounded-[1.8rem] p-6 shadow-sm border border-slate-100 hover:border-navy-100 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">إجمالي الطلاب</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_students']) }}</p>
                    <span class="text-emerald-600 font-bold text-[10px] bg-emerald-50 px-2 py-0.5 rounded-lg inline-block mt-2">مستخدم نشط</span>
                </div>
                <div class="w-14 h-14 bg-gold-100 text-gold-600 rounded-2xl flex items-center justify-center group-hover:bg-gold-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>
@endif
@if(auth()->user()->role === 'super_admin' || in_array('scholarships', auth()->user()->permissions ?? []))        <div class="group bg-white rounded-[1.8rem] p-6 shadow-sm border border-slate-100 hover:border-emerald-200 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">المنح النشطة</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['active_scholarships']) }}</p>
                    <span class="text-emerald-600 font-bold text-[10px] bg-emerald-50 px-2 py-0.5 rounded-lg inline-block mt-2">منحة متاحة الآن</span>
                </div>
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
        </div>
@endif
      <!--   <div class="group bg-white rounded-[1.8rem] p-6 shadow-sm border border-slate-100 hover:border-gold-100 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">الإيرادات</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_revenue'], 1) }}K <span class="text-xs font-bold text-slate-400">ر.س</span></p>
                    <span class="text-gold-600 font-bold text-[10px] bg-gold-100 px-2 py-0.5 rounded-lg inline-block mt-2">إجمالي المدفوعات</span>
                </div>
                <div class="w-14 h-14 bg-gold-100 text-gold-600 rounded-2xl flex items-center justify-center group-hover:bg-gold-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
        </div>  -->

@if(auth()->user()->role === 'super_admin' || in_array('support', auth()->user()->permissions ?? []))
        <div class="group bg-white rounded-[1.8rem] p-6 shadow-sm border border-slate-100 hover:border-orange-200 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">تذاكر معلقة</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['pending_tickets'] }}</p>
                    <span class="text-orange-600 font-bold text-[10px] bg-orange-50 px-2 py-0.5 rounded-lg inline-block mt-2">تحتاج رد</span>
                </div>
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-gold-600 rounded-full"></div>
                <h2 class="text-lg font-black text-slate-800">أحدث طلبات الالتحاق</h2>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50/50 border-b border-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">الطالب</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">المنحة</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase text-center">التاريخ</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase text-center">الحالة</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recent_applications as $app)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($app->user->avatar)
                                    <img src="{{ $app->user->avatar }}" alt="{{ $app->user->name }}" class="w-10 h-10 rounded-xl object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gold-100 rounded-xl flex items-center justify-center font-black text-gold-600 text-xs">
                                        {{ mb_substr($app->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $app->user->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold">#APP-{{ $app->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-700">{{ $app->scholarship->title_ar ?? $app->scholarship->title_en }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <p class="text-[11px] font-bold text-slate-500">{{ $app->created_at->format('Y/m/d') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-orange-50 text-orange-600',
                                    'approved' => 'bg-emerald-50 text-emerald-600',
                                    'rejected' => 'bg-rose-50 text-rose-600'
                                ];
                                $statusLabels = ['pending' => 'قيد الانتظار', 'processing' => 'قيد المعالجة', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black {{ $statusClasses[$app->status] ?? 'bg-slate-50' }}">
                                {{ $statusLabels[$app->status] ?? $app->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('admin.scholarships.edit', $app->scholarship_id) }}" class="p-2 text-slate-400 hover:text-gold-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-bold text-sm">لا توجد طلبات حديثة حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        @if(auth()->user()->role === 'super_admin' || in_array('scholarships', auth()->user()->permissions ?? []))
        <a href="{{ url('/admin/scholarships/create') }}" class="flex items-center justify-center gap-3 p-5 bg-gold-600 text-white rounded-[1.5rem] hover:bg-gold-700 transition-all shadow-lg shadow-navy-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="font-black text-sm">إضافة منحة جديدة</span>
        </a>
        @endif
        @if(auth()->user()->role === 'super_admin' || in_array('students', auth()->user()->permissions ?? []))
        <a href="{{ url('/admin/students/create') }}" class="flex items-center justify-center gap-3 p-5 bg-white text-slate-700 border border-slate-200 rounded-[1.5rem] hover:bg-slate-50 transition-all shadow-sm">
            <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span class="font-black text-sm">تسجيل طالب يدوياً</span>
        </a>
@endif
        <button class="flex items-center justify-center gap-3 p-5 bg-white text-slate-700 border border-slate-200 rounded-[1.5rem] hover:bg-slate-50 transition-all shadow-sm">
            <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="font-black text-sm">تصدير التقارير</span>
        </button>
    </div>
</div>
@endsection