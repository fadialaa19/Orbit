@extends('layouts.dashboard')

@section('title', 'استكشف المنح')

@section('header_search')
{{-- حقل البحث العلوي تم ربطه بـ الـ Form عبر إضافة سمة form="filterForm" لتوحيد البيانات --}}
<div class="hidden md:flex items-center bg-slate-50 px-4 py-2 rounded-xl gap-3 w-80 border border-transparent focus-within:border-navy-100 transition">
    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    <input type="text" name="q" value="{{ request('q') }}" form="filterForm" oninput="debounceSubmit()" placeholder="ابحث عن منحة، جامعة أو تخصص..." class="bg-transparent outline-none text-sm w-full font-bold">
</div>
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10">
    <div class="max-w-7xl mx-auto">
        
        <div class="text-right mb-10">
            <h1 class="text-3xl font-black text-slate-800">استكشف المنح الدراسية</h1>
            <p class="text-slate-500 font-bold mt-2">{{ $scholarships->total() }} منحة متاحة</p>
        </div>

        {{-- بداية الفورم الرئيسي --}}
        @php
            $activeFiltersCount = count((array) request('coverage')) + count((array) request('category'));
        @endphp
        <form method="GET" action="{{ route('dashboard.scholarships') }}" id="filterForm" x-data="{ filtersOpen: {{ $activeFiltersCount > 0 ? 'true' : 'false' }} }">

            <div class="bg-white p-4 rounded-[2rem] shadow-sm mb-4 flex gap-3 border border-slate-100 overflow-hidden">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث عن منحة، جامعة أو تخصص..." class="flex-1 min-w-0 bg-transparent pr-4 outline-none font-medium text-right">
                <button type="button" @click="filtersOpen = !filtersOpen" class="relative flex items-center gap-2 px-5 py-3 rounded-2xl font-bold text-sm transition" :class="filtersOpen ? 'bg-gold-100 text-gold-600' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    الفلاتر
                    @if($activeFiltersCount > 0)
                    <span class="bg-gold-600 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center">{{ $activeFiltersCount }}</span>
                    @endif
                </button>
                <button type="submit" class="bg-gold-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-gold-100 hover:bg-gold-700 transition">بحث</button>
            </div>

            {{-- لوحة الفلاتر: منسدلة أفقياً بدل شريط جانبي ثابت، حتى ما تزاحم السايدبار --}}
            <div x-show="filtersOpen" x-cloak class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-slate-800">الفلاتر</h3>
                    <a href="{{ route('dashboard.scholarships') }}" class="text-xs text-gold-600 font-bold hover:underline">إعادة تعيين</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- فلتر مميزات التغطية والتمويل (Coverage الفعلي بقاعدة البيانات) --}}
                    <div>
                        <h4 class="text-sm font-black text-slate-700 mb-4 text-right">مزايا التمويل</h4>
                        <div class="flex flex-wrap gap-3 justify-end">
                            @foreach(['تمويل كامل' => 'ممولة بالكامل', 'إعفاء من الرسوم' => 'إعفاء من الرسوم', 'راتب شهري' => 'راتب شهري', 'سكن جامعي' => 'سكن جامعي'] as $value => $label)
                            <label class="flex items-center gap-2 cursor-pointer group bg-slate-50 hover:bg-slate-100 px-3 py-2 rounded-xl">
                                <span class="text-sm font-bold text-slate-500 group-hover:text-slate-800">{{ $label }}</span>
                                <input type="checkbox" name="coverage[]" value="{{ $value }}" onchange="this.form.submit()" {{ in_array($value, (array)request('coverage')) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-200 text-gold-600 focus:ring-gold-500">
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- فلتر التصنيف والمستوى الأكاديمي متوافق مع خيارات قاعدة البيانات (Category) --}}
                    <div>
                        <h4 class="text-sm font-black text-slate-700 mb-4 text-right">المستوى الأكاديمي</h4>
                        <div class="flex flex-wrap gap-3 justify-end">
                            @foreach(['Bachelor' => 'بكالوريوس', 'Master' => 'ماجستير', 'PhD' => 'دكتوراه', 'Short Course' => 'كورسات قصيرة وزمالات'] as $key => $level)
                            <label class="flex items-center gap-2 cursor-pointer group bg-slate-50 hover:bg-slate-100 px-3 py-2 rounded-xl">
                                <span class="text-sm font-bold text-slate-500 group-hover:text-slate-800">{{ $level }}</span>
                                <input type="checkbox" name="category[]" value="{{ $key }}" onchange="this.form.submit()" {{ in_array($key, (array)request('category')) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-200 text-gold-600 focus:ring-gold-500">
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- قسم عرض المنح الرئيسي --}}
            <div class="space-y-6">
                @forelse($scholarships as $scholarship)
                    @include('dashboard.partials.scholarship-card', ['scholarship' => $scholarship, 'matchScores' => $matchScores])
                    @empty
                    <div class="text-center py-20 bg-white rounded-[2rem] border">
                        <p class="text-slate-400 font-bold">لا توجد منح دراسية تطابق خيارات البحث الحالية.</p>
                    </div>
                    @endforelse
                    
                    <div class="py-10">
                        {{ $scholarships->appends(request()->query())->links() }}
                    </div>
                </div>
        </form>
    </div>
</div>

<script>
    // دالة لتأخير الإرسال التلقائي للبحث العلوي منعاً للضغط المتكرر العشوائي
    let timeout = null;
    function debounceSubmit() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    }

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
                // نحافظ على نفس صفوف الموضع اللي كانت موجودة أصلاً (bottom-4/left-4 داخل
                // صورة الغلاف، أو top-5/left-6 بدون صورة) بدل استبدالها بموضع ثابت واحد،
                // حتى ما ترجع الشارة تنقص وتُقتطع بزاوية الكارد المدوّرة.
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