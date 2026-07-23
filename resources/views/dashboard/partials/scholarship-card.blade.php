{{-- كارد منحة موحّد، يُستخدم بصفحة استكشاف المنح وصفحة المفضلات حتى يبقى المظهر متطابقاً --}}
@php
    $matchScores = $matchScores ?? [];
@endphp
<div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-50 hover:border-gold-100 transition duration-300 relative group overflow-hidden">

    {{-- رابط يغطي الكارد بالكامل حتى الضغط بأي مكان (الصورة، اللوجو، العنوان) ينقل
         لصفحة تفاصيل المنحة - بستوي z-index أقل من أزرار التفاعل الفعلية حتى تضل
         هي القابلة للنقر بشكل منفصل فوقه. --}}
    <a href="{{ route('dashboard.scholarships.show', $scholarship->id) }}" class="absolute inset-0 z-10" aria-label="{{ $scholarship->title_ar }}"></a>

    @if($scholarship->main_image)
        <div class="w-full aspect-[2/1] md:aspect-[5/1] overflow-hidden relative bg-gradient-to-br from-slate-100 to-slate-50">
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
        <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 rounded-2xl overflow-hidden border border-slate-100 bg-slate-50 flex items-center justify-center p-2 relative z-10 pointer-events-none {{ $scholarship->main_image ? 'ring-4 ring-white shadow-lg' : '' }}">
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

        <div class="relative z-20 flex flex-col gap-3 w-full md:w-auto items-center md:items-end">
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
