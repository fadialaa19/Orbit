@extends('layouts.admin')

@section('title', 'إدارة الطلاب')
@section('breadcrumb', 'الطلاب')

@section('content')
<div x-data="{ 
    addModal: {{ request()->routeIs('*.create') ? 'true' : 'false' }}, 
    editModal: false, 
    currentUser: {} 
}" class="max-w-full mx-auto">
    
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition.duration.500ms
             class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] min-w-[300px] mb-4 p-4 bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-2xl flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 leading-tight">إدارة الطلاب</h1>
            <p class="text-xs font-bold text-slate-400 mt-1">عرض وتحليل بيانات جميع المنتسبين للنظام</p>
        </div>
        <button @click="addModal = true" class="bg-gold-600 text-white px-6 py-3 rounded-2xl font-black text-sm shadow-lg shadow-gold-100 hover:bg-gold-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            إضافة طالب جديد
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-gold-100 text-gold-600 rounded-xl flex items-center justify-center font-black text-xl">Σ</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">الإجمالي</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black">✓</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">نشط</p>
                <p class="text-xl font-black text-emerald-600">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-black">!</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">بانتظار المراجعة</p>
                <p class="text-xl font-black text-amber-600">{{ $stats['pending'] }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-black">X</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">معطل</p>
                <p class="text-xl font-black text-rose-600">{{ $stats['inactive'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <form action="{{ route('admin.students.index') }}" method="GET" class="relative max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن اسم، بريد..." class="w-full bg-slate-50 border border-slate-100 focus:bg-white focus:border-gold-300 rounded-xl px-10 py-2.5 text-xs font-bold transition-all outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">الطالب</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">الحالة</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($students as $student)
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($student->avatar)
                                    <img src="{{ $student->avatar }}" alt="{{ $student->name }}" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-gold-600 to-gold-400 flex items-center justify-center text-white font-black shadow-sm">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                @endif
                                <p class="font-bold text-slate-800 text-sm">{{ $student->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-500">{{ $student->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusMap = [
                                    'active' => ['bg-emerald-100 text-emerald-700', 'نشط'],
                                    'pending' => ['bg-amber-100 text-amber-700', 'بانتظار المراجعة'],
                                    'inactive' => ['bg-rose-100 text-rose-700', 'معطل']
                                ];
                                $current = $statusMap[$student->status] ?? ['bg-slate-100', 'غير معروف'];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $current[0] }}">
                                {{ $current[1] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                @unless($student->email_verified_at)
                                <form action="{{ route('admin.students.verify-email', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('تفعيل حساب هذا الطالب يدوياً بدون تأكيد الإيميل؟')">
                                    @csrf
                                    <button type="submit" title="تفعيل الحساب يدوياً" class="p-2 text-slate-400 hover:text-emerald-600 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                </form>
                                @endunless
                                <a href="{{ route('admin.students.show', $student->id) }}"
                                   title="عرض الملف الكامل"
                                   class="p-2 text-slate-400 hover:text-navy-700 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button @click="currentUser = {{ $student }}; $refs.editForm.action = '/admin/students/' + {{ $student->id }}; editModal = true"
                                        title="تعديل"
                                        class="p-2 text-slate-400 hover:text-gold-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب نهائياً؟')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50/50 border-t border-slate-50">
            {{ $students->links() }}
        </div>
    </div>

    <div x-show="addModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="addModal = false" class="bg-white w-full max-w-md rounded-[2rem] p-8 shadow-2xl animate-in zoom-in-95 duration-200">
            <h2 class="text-xl font-black text-slate-900 mb-6">إضافة طالب جديد</h2>
            <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">الاسم الكامل</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">كلمة المرور</label>
                    <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">الحالة</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none cursor-pointer">
                        <option value="active">نشط</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="inactive">معطل</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-1 bg-gold-600 text-white py-3 rounded-xl font-black text-xs shadow-lg hover:bg-gold-700 transition">حفظ الطالب</button>
                    <button type="button" @click="addModal = false" class="flex-1 bg-slate-50 text-slate-400 py-3 rounded-xl font-black text-xs">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="editModal = false" class="bg-white w-full max-w-md rounded-[2rem] p-8 shadow-2xl">
            <h2 class="text-xl font-black text-slate-900 mb-6">تعديل بيانات الطالب</h2>
            <form x-ref="editForm" method="POST" class="space-y-4">
                @csrf 
                @method('PUT')
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">الاسم الكامل</label>
                    <input type="text" name="name" x-model="currentUser.name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" x-model="currentUser.email" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-gold-300">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">كلمة المرور (اختياري)</label>
                    <input type="password" name="password" placeholder="اتركها فارغة للأبقاء على القديمة" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">الحالة</label>
                    <select name="status" x-model="currentUser.status" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold outline-none">
                        <option value="active">نشط</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="inactive">معطل</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="submit" class="flex-1 bg-gold-600 text-white py-3 rounded-xl font-black text-xs shadow-lg hover:bg-gold-700 transition">تحديث البيانات</button>
                    <button type="button" @click="editModal = false" class="flex-1 bg-slate-50 text-slate-400 py-3 rounded-xl font-black text-xs">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection