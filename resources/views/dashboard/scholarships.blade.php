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

            <div class="bg-white p-4 rounded-[2rem] shadow-sm mb-4 flex gap-3 border border-slate-100">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث عن منحة، جامعة أو تخصص..." class="flex-1 bg-transparent pr-4 outline-none font-medium text-right">
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
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 hover:border-gold-100 transition duration-300 relative group overflow-hidden">

                        @if($scholarship->main_image)
                            <div class="w-full aspect-[5/1] overflow-hidden relative bg-gradient-to-br from-slate-100 to-slate-50">
                                <img src="{{ $scholarship->main_image }}" alt="" class="w-full h-full object-cover">

                                {{-- نسبة التوافق الذكية: مخزّنة مسبقًا أو بتتحلل بالخلفية - محاطة داخل صورة
                                     الغلاف نفسها وملاصقة لأسفلها، بعيدة عن زاوية الكارد المدوّرة العلوية
                                     حتى ما تنقص وتُقتطع بصرياً. --}}
                                @if(isset($matchScores[$scholarship->id]))
                                    @php
                                        $score = $matchScores[$scholarship->id];
                                        $scoreColor = $score >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($score >= 40 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100');
                                    @endphp
                                    <div class="absolute bottom-4 left-4 z-20 px-3 py-1.5 rounded-full text-[11px] font-black border shadow-sm {{ $scoreColor }}" data-match-badge="{{ $scholarship->id }}">
                                        🎯 نسبة توافقك: {{ $score }}%
                                    </div>
                                @else
                                    <div class="absolute bottom-4 left-4 z-20 px-3 py-1.5 rounded-full text-[11px] font-black border shadow-sm bg-slate-50 text-slate-400 border-slate-100 animate-pulse" data-match-badge="{{ $scholarship->id }}" data-match-pending="1">
                                        🤖 جارِ تحليل التوافق...
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- بدون صورة غلاف: الشارة تضل بأعلى الكارد كالسابق (بدون صورة، اقتطاع الزاوية
                                 غير ملحوظ لأنه فوق خلفية بيضاء بسيطة). --}}
                            @if(isset($matchScores[$scholarship->id]))
                                @php
                                    $score = $matchScores[$scholarship->id];
                                    $scoreColor = $score >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($score >= 40 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100');
                                @endphp
                                <div class="absolute top-5 left-6 z-20 px-3 py-1.5 rounded-full text-[11px] font-black border shadow-sm {{ $scoreColor }}" data-match-badge="{{ $scholarship->id }}">
                                    🎯 نسبة توافقك: {{ $score }}%
                                </div>
                            @else
                                <div class="absolute top-5 left-6 z-20 px-3 py-1.5 rounded-full text-[11px] font-black border shadow-sm bg-slate-50 text-slate-400 border-slate-100 animate-pulse" data-match-badge="{{ $scholarship->id }}" data-match-pending="1">
                                    🤖 جارِ تحليل التوافق...
                                </div>
                            @endif
                        @endif

                        <div class="p-8">
                        <div class="flex flex-col md:flex-row gap-8 items-center">

                            {{-- صندوق اللوجو المعتمد على logo_image الفعلي والـ Fallback له --}}
                            <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 rounded-2xl overflow-hidden border border-slate-100 bg-slate-50 flex items-center justify-center p-2 relative z-10 {{ $scholarship->main_image ? 'ring-4 ring-white shadow-lg' : '' }}">
                                @if($scholarship->logo_image)
                                    <img src="{{ $scholarship->logo_image }}" alt="{{ $scholarship->title_ar }}" class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full bg-gold-100 rounded-xl flex items-center justify-center text-gold-600">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174L11.25 15.89c.445.365 1.055.365 1.5 0l6.99-5.717m-.003 4.31v4.454a2.25 2.25 0 01-2.247 2.247H6.75a2.25 2.25 0 01-2.247-2.247v-4.454m15.122-4.31L12 3l-8.12 6.634m16.24 0l-1.92 11.52H5.8l-1.92-11.52z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 text-center md:text-right">
                                <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-[10px] font-black">🌍 {{ $scholarship->country }}</span>
                                    <h2 class="text-xl font-black text-slate-800">{{ $scholarship->title_ar }}</h2>
                                </div>
                                <p class="text-sm text-slate-400 font-bold mb-4">{{ $scholarship->university }}</p>
                                
                                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                                    {{-- تعديل حقل الـ value ليقرأ من الحقل الفعلي financial_value --}}
                                    <div class="flex items-center gap-2 bg-green-50 text-green-600 px-3 py-1.5 rounded-xl text-xs font-black">
                                        <span>💰</span> {{ $scholarship->financial_value ?? 'تمويل مرن' }}
                                    </div>
                                    {{-- المراحل الدراسية المتعددة للمنحة، مترجمة عربي --}}
                                    <div class="flex items-center gap-2 bg-gold-100 text-gold-600 px-3 py-1.5 rounded-xl text-xs font-black">
                                        <span>🎓</span> {{ $scholarship->category_label }}
                                    </div>
                                    <div class="flex items-center gap-2 bg-slate-50 text-slate-500 px-3 py-1.5 rounded-xl text-xs font-black">
                                        <span>📅</span> {{ $scholarship->formatted_deadline }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 w-full md:w-auto items-center md:items-end">
                                {{-- جلب أول وسم موصى به من مصفوفة الـ recommended_tags --}}
                                @if(is_array($scholarship->recommended_tags) && count($scholarship->recommended_tags) > 0)
                                <span class="text-[10px] font-black py-1 px-3 rounded-lg shadow-sm border bg-amber-50 text-amber-700 border-amber-100">
                                    🔥 {{ $scholarship->recommended_tags[0] }}
                                </span>
                                @endif

                                <a href="{{ route('dashboard.scholarships.show', $scholarship->id) }}" class="bg-gold-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-gold-100 hover:bg-gold-700 transition text-center block w-full md:w-auto">
                                    عرض التفاصيل
                                </a>

                                @php
                                    $isFavorited = auth()->check() && $scholarship->favoritedBy()->where('user_id', auth()->id())->exists();
                                @endphp

                                <div class="flex gap-2">
                                    <button type="submit" formaction="{{ route('dashboard.scholarships.favorite', $scholarship->id) }}" formmethod="POST" class="w-full h-full flex items-center justify-center rounded-xl p-3 transition flex-1 {{ $isFavorited ? 'bg-red-50 text-red-500' : 'bg-slate-50 hover:bg-red-50 hover:text-red-500 text-slate-400' }}" title="{{ $isFavorited ? 'إزالة من المفضلة' : 'حفظ' }}">
                                        @csrf
                                        <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>

                                    <button type="button" onclick="navigator.clipboard.writeText('{{ route('dashboard.scholarships.show', $scholarship->id) }}'); alert('تم نسخ رابط المنحة بنجاح!')" class="flex-1 bg-slate-50 hover:bg-gold-100 hover:text-gold-600 text-slate-400 p-3 rounded-xl transition flex items-center justify-center" title="مشاركة">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367 2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
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