@extends('layouts.dashboard')
@section('title', 'المفضلات')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl">
    <div class="max-w-7xl mx-auto">

        <div class="text-right mb-10">
            <h1 class="text-3xl font-black text-slate-800">المفضلات <span class="text-2xl text-gold-600">({{ $scholarships->count() }})</span></h1>
            <p class="text-slate-500 font-bold mt-2">المنح التي اخترتها للمتابعة لاحقاً</p>
        </div>

        <form method="POST" class="space-y-6">
            @forelse($scholarships as $scholarship)
                @include('dashboard.partials.scholarship-card', ['scholarship' => $scholarship, 'matchScores' => $matchScores])
            @empty
                <div class="text-center py-20 bg-white rounded-[2rem] border border-slate-100">
                    <div class="text-6xl opacity-20">❤️</div>
                    <p class="text-slate-400 font-bold mt-3">لا توجد مفضلات حالياً</p>
                </div>
            @endforelse
        </form>
    </div>
</div>

<script>
    // تحليل نسبة التوافق الذكية للمنح اللي لسه ما اتحسبتش، بدون ما نأخر تحميل الصفحة
    (function() {
        const pendingIds = @json($matchMissing ?? []);
        if (!pendingIds.length) return;

        const token = document.querySelector('meta[name="csrf-token"]').content;
        fetch('{{ route('dashboard.scholarships.match-scores') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ scholarship_ids: pendingIds })
        })
        .then(res => res.json())
        .then(data => {
            Object.entries(data.scores || {}).forEach(([scholarshipId, info]) => {
                const badge = document.querySelector(`[data-match-badge="${scholarshipId}"]`);
                if (!badge) return;
                const score = info.score;
                const colorClass = score >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                    : score >= 40 ? 'bg-amber-50 text-amber-700 border-amber-100'
                    : 'bg-rose-50 text-rose-700 border-rose-100';
                const positionClass = badge.classList.contains('bottom-4') ? 'bottom-4 left-4' : 'top-5 left-6';
                badge.className = 'absolute ' + positionClass + ' z-20 px-3 py-1.5 rounded-full text-[11px] font-black border shadow-sm ' + colorClass;
                badge.removeAttribute('data-match-pending');
                badge.textContent = '🎯 نسبة توافقك: ' + score + '%';
            });
        })
        .catch(err => console.error('Match score fetch error', err));
    })();
</script>
@endsection
