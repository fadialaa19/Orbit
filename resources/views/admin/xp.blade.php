@extends('layouts.admin')

@section('title', 'التحكم بنقاط XP')
@section('breadcrumb', 'نقاط XP')

@section('content')
<div x-data="{
    modalOpen: false,
    target: { id: null, name: '', xp: 0 },
    amount: '',
    reason: '',
    openFor(id, name, xp) {
        this.target = { id, name, xp };
        this.amount = '';
        this.reason = '';
        this.modalOpen = true;
    }
}" class="max-w-full mx-auto space-y-8">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] min-w-[300px] p-4 bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-2xl flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div>
        <h1 class="text-2xl font-black text-slate-900">التحكم بنقاط XP</h1>
        <p class="text-xs font-bold text-slate-400 mt-1">أضف أو انقص نقاط أي طالب مباشرة، مع توثيق السبب لكل تعديل</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <form method="GET" class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-4">
                <div class="relative">
                    <svg class="w-5 h-5 text-slate-300 absolute right-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="q" value="{{ $search }}" placeholder="ابحث باسم الطالب أو إيميله..."
                           class="w-full bg-slate-50 border border-slate-100 rounded-xl pr-11 pl-4 py-3 text-sm font-bold outline-none focus:border-gold-300 transition">
                </div>
            </form>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-slate-50/50">
                            <tr class="border-b border-slate-100">
                                <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">الطالب</th>
                                <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">رصيد XP</th>
                                <th class="p-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($students as $student)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-gold-100 flex items-center justify-center text-gold-700 font-black text-sm shrink-0">
                                            @if($student->avatar)
                                                <img src="{{ $student->avatar }}" class="w-full h-full object-cover">
                                            @else
                                                {{ mb_substr($student->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-black text-sm text-slate-800">{{ $student->name }}</p>
                                            <p class="text-[11px] text-slate-400 font-bold">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-gold-50 text-gold-700 border border-gold-100">⚡ {{ $student->xp }} XP</span>
                                </td>
                                <td class="p-5">
                                    <button type="button" @click="openFor({{ $student->id }}, @js($student->name), {{ $student->xp }})"
                                            class="px-4 py-2 bg-navy-900 text-white rounded-xl text-xs font-black hover:bg-navy-800 transition">
                                        تعديل الرصيد
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-10 text-center text-slate-400 font-bold text-sm">لا يوجد طلاب مطابقين</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-50">{{ $students->links() }}</div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 h-fit">
            <h3 class="font-black text-slate-800 mb-5">آخر التعديلات</h3>
            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                @forelse($recentTransactions as $tx)
                <div class="flex items-start gap-3 pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black shrink-0 {{ $tx->amount > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-black text-slate-700 truncate">{{ $tx->user->name ?? 'طالب محذوف' }}</p>
                        <p class="text-[11px] text-slate-400 font-bold truncate">{{ $tx->reason }}</p>
                        <p class="text-[10px] text-slate-300 font-bold mt-0.5">
                            {{ $tx->created_by ? '👤 ' . ($tx->admin->name ?? 'أدمن') : '⚙️ تلقائي' }} · {{ $tx->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 font-bold text-xs py-6">لا يوجد تعديلات بعد</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- مودال التعديل --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100]">
        <div @click="modalOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-0"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 z-10 pointer-events-none">
            <div x-show="modalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md pointer-events-auto">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-lg text-slate-900">تعديل رصيد النقاط</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1" x-text="target.name + ' · الرصيد الحالي: ' + target.xp + ' XP'"></p>
                    </div>
                    <button @click="modalOpen = false" class="p-2 hover:bg-slate-50 rounded-xl transition">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="'{{ url('/admin/xp') }}/' + target.id + '/adjust'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">المقدار (استخدم إشارة سالب للنقصان مثل -50-)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="amount = amount ? -Math.abs(amount) : -50"
                                    class="w-11 h-11 shrink-0 bg-rose-50 text-rose-600 rounded-xl font-black hover:bg-rose-100 transition">−</button>
                            <input type="number" name="amount" x-model="amount" required
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-center text-lg font-black text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition">
                            <button type="button" @click="amount = amount ? Math.abs(amount) : 50"
                                    class="w-11 h-11 shrink-0 bg-emerald-50 text-emerald-600 rounded-xl font-black hover:bg-emerald-100 transition">+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">السبب (سيظهر للطالب)</label>
                        <textarea name="reason" x-model="reason" required rows="2" placeholder="مثال: مكافأة المشاركة بفعالية المجتمع"
                                  class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:border-gold-500 focus:bg-white outline-none transition"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gold-600 text-white py-3.5 rounded-xl font-black text-sm hover:bg-gold-700 shadow-lg shadow-gold-100 transition-all">
                        تأكيد التعديل
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
