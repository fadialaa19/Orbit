@extends('layouts.admin')

@section('title', 'إدارة الإعلانات')
@section('breadcrumb', 'لوحة الإعلانات')

@section('content')
<div x-data="{
    showModal: false,
    editMode: false,
    currentAnnouncement: { icon: '', title: '', body: '', type: 'info', sort_order: 0 },
    storeUrl: '{{ route('admin.announcements.store') }}',
    updateUrl() {
        return this.currentAnnouncement ? '{{ url('/admin/announcements') }}/' + this.currentAnnouncement.id : '';
    }
}" class="max-w-full mx-auto space-y-8">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] min-w-[300px] p-4 bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-2xl flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900">لوحة الإعلانات</h1>
            <p class="text-xs font-bold text-slate-400 mt-1">إعلانات تظهر بشكل بارز أعلى لوحة تحكم الطالب (نتائج، مواعيد، تنبيهات رسمية...)</p>
        </div>
        <button @click="showModal = true; editMode = false; currentAnnouncement = { icon: '', title: '', body: '', type: 'info', sort_order: 0 };"
                class="bg-gold-600 text-white px-6 py-3 rounded-2xl font-black text-sm hover:bg-gold-700 transition-all shadow-lg shadow-gold-100 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            إضافة إعلان جديد
        </button>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50/50">
                    <tr class="border-b border-slate-100">
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">الإعلان</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">النوع</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">الترتيب</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">الحالة</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $typeMap = [
                            'info' => ['bg-navy-100 text-navy-700', 'معلومة'],
                            'warning' => ['bg-amber-100 text-amber-700', 'تنبيه'],
                            'urgent' => ['bg-rose-100 text-rose-700', 'عاجل'],
                        ];
                    @endphp
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gold-50 rounded-xl flex items-center justify-center text-xl shrink-0">
                                    {{ $announcement->icon }}
                                </div>
                                <div>
                                    <p class="font-black text-sm text-slate-900">{{ $announcement->title }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 max-w-xs truncate">{{ $announcement->body }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="px-3 py-1.5 rounded-xl text-[10px] font-black {{ $typeMap[$announcement->type][0] ?? $typeMap['info'][0] }}">{{ $typeMap[$announcement->type][1] ?? $announcement->type }}</span>
                        </td>
                        <td class="p-6">
                            <p class="font-bold text-sm text-slate-700">{{ $announcement->sort_order }}</p>
                        </td>
                        <td class="p-6">
                            <form action="{{ route('admin.announcements.toggle', $announcement) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="{{ $announcement->is_active ? 'إخفاء الإعلان' : 'إظهار الإعلان' }}"
                                        class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all {{ $announcement->is_active ? 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                    {{ $announcement->is_active ? '✅ ظاهر' : '🚫 مخفي' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2">
                                <button @click="showModal = true; editMode = true; currentAnnouncement = {{ json_encode($announcement->only(['id','icon','title','body','type','sort_order']), JSON_UNESCAPED_UNICODE) }};"
                                        class="p-2 bg-gold-100 text-gold-600 rounded-xl hover:bg-gold-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <span class="text-5xl block mb-4">📢</span>
                            <p class="text-slate-400 font-bold">لا توجد إعلانات بعد</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[100]">
            <div @click="showModal = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-0"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4 z-10 pointer-events-none">
                <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto pointer-events-auto">

                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-black text-lg text-slate-900" x-text="editMode ? 'تعديل الإعلان' : 'إضافة إعلان جديد'"></h3>
                        <button @click="showModal = false" class="p-2 hover:bg-slate-50 rounded-xl transition">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form :action="editMode ? updateUrl() : storeUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>

                        <div class="grid grid-cols-4 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">أيقونة</label>
                                <input type="text" name="icon" x-model="currentAnnouncement.icon" placeholder="📢" maxlength="10"
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-3 py-3 text-center text-lg focus:border-gold-500 focus:bg-white outline-none transition">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">عنوان الإعلان</label>
                                <input type="text" name="title" x-model="currentAnnouncement.title" required
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">نص الإعلان</label>
                            <textarea name="body" x-model="currentAnnouncement.body" required rows="3"
                                      class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">نوع الإعلان</label>
                            <select name="type" x-model="currentAnnouncement.type"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition">
                                <option value="info">معلومة (كحلي)</option>
                                <option value="warning">تنبيه (كهرماني)</option>
                                <option value="urgent">عاجل (أحمر)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">ترتيب الظهور (الأصغر يظهر أولاً)</label>
                            <input type="number" name="sort_order" x-model="currentAnnouncement.sort_order" min="0"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-gold-600 text-white py-3.5 rounded-xl font-black text-sm hover:bg-gold-700 shadow-lg shadow-gold-100 transition-all">
                                <span x-text="editMode ? 'حفظ التعديلات' : 'إضافة الإعلان'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
