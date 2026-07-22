@extends('layouts.dashboard')
@section('title', 'نقاطي')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl"
     x-data="{
        copied: false,
        shareUrl: '{{ url('/register?ref=' . auth()->id()) }}',
        seconds: {{ (int) $user->total_time_spent_seconds }},
        tick: null,
        init() {
            this.tick = setInterval(() => { this.seconds++ }, 1000);
        },
        // عداد الساعة الحالية: بيعد من 00:00 لـ 59:59 وبعدين يرجع للصفر من جديد،
        // يمثّل تقدمك الفعلي نحو الـ 25 XP يلي بتاخدهم كل ما تكمّل ساعة كاملة.
        formattedCycle() {
            const remainder = this.seconds % 3600;
            const m = Math.floor(remainder / 60);
            const s = remainder % 60;
            const pad = n => n.toString().padStart(2, '0');
            return pad(m) + ':' + pad(s);
        },
        hoursCompleted() {
            return Math.floor(this.seconds / 3600);
        }
     }">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">⚡ نقاط XP الخاصة فيك</h1>
            <p class="text-slate-500 font-bold mt-2">كل شي تحتاج تعرفه عن نظام النقاط بأوربيت، وكيف تستفيد منه</p>
        </div>

        {{-- بطاقة المستوى والوقت --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-black text-slate-700">المستوى {{ $currentLevel }}</span>
                    <span class="text-xs font-bold text-slate-400">{{ $xpInCurrentLevel }}/1000 XP</span>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden mb-3">
                    <div class="h-full bg-gradient-to-r from-gold-500 to-gold-600 rounded-full transition-all duration-1000" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <p class="text-xs font-bold text-slate-400">تبقى {{ $xpRemaining }} XP للمستوى التالي</p>
                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-sm font-black text-slate-700">رصيدك الإجمالي</span>
                    <span class="text-2xl font-black text-gold-600">{{ $user->xp }} XP</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-navy-900 to-navy-950 rounded-[2.5rem] p-8 shadow-sm text-white relative overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-black text-gold-300 mb-2">⏱️ تقدمك نحو الساعة القادمة</p>
                        <p class="text-4xl md:text-5xl font-black tracking-wider" style="font-variant-numeric: tabular-nums;" x-text="formattedCycle()"></p>
                        <p class="text-xs font-bold text-slate-300 mt-3">دقيقة : ثانية — كل ساعة كاملة بتضيفلك 25 XP تلقائياً</p>
                    </div>
                    <div class="shrink-0 bg-white/10 backdrop-blur rounded-2xl px-4 py-3 text-center border border-white/10">
                        <p class="text-2xl font-black text-gold-300" x-text="hoursCompleted()"></p>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-wider mt-1 leading-tight">ساعة<br>مكتملة</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- شرح آلية النقاط --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 mb-8">
            <h3 class="font-black text-slate-800 text-lg mb-6">كيف تجمع نقاط XP؟</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                    <span class="w-12 h-12 rounded-2xl bg-gold-100 text-gold-600 flex items-center justify-center text-xl mb-4">🤝</span>
                    <p class="font-black text-slate-800 mb-2">+25 XP لكل دعوة ناجحة</p>
                    <p class="text-xs font-bold text-slate-500 leading-relaxed">لما تدعو صديق ويسجل من رابطك، بتاخد 25 نقطة - بس بعد ما يكمّل صديقك 50% من ملفه الشخصي (اسمه، بياناته، مستنداته)، مش لمجرد إنه سجّل. هيك منضمن إنه الدعوة حقيقية ومش حساب فاضي.</p>
                </div>
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                    <span class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mb-4">⏱️</span>
                    <p class="font-black text-slate-800 mb-2">+25 XP لكل ساعة تصفح</p>
                    <p class="text-xs font-bold text-slate-500 leading-relaxed">كل ساعة فعلية تقضيها متصفح الموقع (مش مجرد فاتح تاب وغير موجود) بتضيفلك 25 نقطة تلقائياً بدون أي إجراء منك.</p>
                </div>
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                    <span class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-4">🏆</span>
                    <p class="font-black text-slate-800 mb-2">كل 1000 XP = تقديم مجاني</p>
                    <p class="text-xs font-bold text-slate-500 leading-relaxed">كل ما توصل لعتبة 1000 نقطة جديدة (1000، 2000، 3000...) بيتفتح لك تقديم مجاني على منحة من طرف فريقنا - بيتواصل معك فريق الدعم مباشرة.</p>
                </div>
            </div>
        </div>

        {{-- رابط الدعوة --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-gold-500 via-gold-600 to-amber-600 p-8 rounded-[2.5rem] shadow-xl shadow-gold-200/50 mb-8">
            <div class="absolute -top-8 -left-8 w-32 h-32 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-10 -right-6 w-28 h-28 bg-white/10 rounded-full"></div>

            <div class="relative flex items-center gap-3 mb-5">
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-3xl">🎁</div>
                <div>
                    <h4 class="font-black text-white text-lg">رابط دعوتك الخاص</h4>
                    <p class="text-sm text-gold-50 font-bold mt-0.5">شاركه مع أصدقائك واكسب 25 XP عن كل صديق يفعّل حسابه</p>
                </div>
            </div>

            <div class="relative flex items-center gap-2 bg-white/15 backdrop-blur p-2 rounded-2xl border border-white/20 overflow-hidden">
                <input type="text" readonly :value="shareUrl" class="bg-transparent border-0 text-sm font-bold px-3 text-white focus:ring-0 flex-1 min-w-0 truncate select-all" dir="ltr">
                <button @click="
                        navigator.clipboard.writeText(shareUrl);
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                        if (window.confetti) { window.confetti({ particleCount: 90, spread: 70, origin: { y: 0.7 }, colors: ['#f5c518','#ffffff','#1a2942'] }); }
                    "
                        :class="copied ? 'bg-emerald-500 text-white' : 'bg-white text-gold-700 hover:bg-gold-50'"
                        class="px-6 py-3 rounded-xl font-black text-sm transition duration-300 flex-shrink-0 shadow-sm">
                    <span x-text="copied ? '✅ تم النسخ!' : 'نسخ الرابط'"></span>
                </button>
            </div>
        </div>

        {{-- سجل النقاط --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
            <h3 class="font-black text-slate-800 text-lg mb-6">سجل نقاطك</h3>
            <div class="space-y-3">
                @forelse($transactions as $tx)
                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black shrink-0 {{ $tx->amount > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                            {{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-black text-slate-700 truncate">{{ $tx->reason }}</p>
                            <p class="text-[11px] text-slate-400 font-bold">{{ $tx->created_by ? 'بواسطة الإدارة' : 'تلقائي' }} · {{ $tx->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 font-bold text-sm py-8">لسه ما في أي نقاط مسجلة - ابدأ بدعوة أصدقائك أو تصفح الموقع لتكسب أول نقاطك!</p>
                @endforelse
            </div>
            @if($transactions->hasPages())
                <div class="mt-6">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
