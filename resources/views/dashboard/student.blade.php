@extends('layouts.dashboard')
@section('title', 'الرئيسية')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 md:px-10" dir="rtl">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">
                    مرحباً، {{ $student->name }}! 👋
                </h1>
                <p class="text-slate-500 font-bold mt-2">تواصل رحلتك نحو تحقيق أحلامك الدراسية</p>
            </div>
            
            @auth
            @php
                $currentXp = auth()->user()->xp;
                $xpPerLevel = 1000; // النقاط المطلوبة لكل مستوى
                
                // حساب المستوى الحالي (مثال: 1250 XP تعني المستوى 2)
                $currentLevel = floor($currentXp / $xpPerLevel) + 1;
                
                // حساب الـ XP الحالي داخل المستوى نفسه (باقي القسمة)
                $xpInCurrentLevel = $currentXp % $xpPerLevel;
                
                // حساب كم متبقي للمستوى التالي
                $xpRemaining = $xpPerLevel - $xpInCurrentLevel;
                
                // حساب النسبة المئوية لعرضها في شريط Tailwind التلقائي
                $progressPercentage = ($xpInCurrentLevel / $xpPerLevel) * 100;
            @endphp

            <div class="w-64 bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-1 text-xs font-semibold text-slate-700">
                    <span>المستوى {{ $currentLevel }}</span>
                    <span>{{ $xpInCurrentLevel }}/{{ $xpPerLevel }} XP</span>
                </div>
                
                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-gold-500 to-gold-500 rounded-full transition-all duration-500" 
                         style="width: {{ $progressPercentage }}%"></div>
                </div>
                
                <p class="text-[10px] text-slate-400 mt-1 text-left">
                    تبقى {{ $xpRemaining }} XP للمستوى التالي
                </p>
            </div>
            @endauth

        </div>

        @if($announcements->isNotEmpty())
        @php
            $announcementsJson = $announcements->map(fn ($a) => [
                'id' => $a->id,
                'icon' => $a->icon,
                'title' => $a->title,
                'body' => $a->body,
                'type' => $a->type,
            ]);
        @endphp
        <script type="application/json" id="announcements-data">@json($announcementsJson)</script>
        <div class="mb-8" x-cloak x-show="current" x-data="{
                all: JSON.parse(document.getElementById('announcements-data').textContent),
                dismissed: JSON.parse(localStorage.getItem('dismissedAnnouncements') || '[]'),
                index: 0,
                timer: null,
                typeClasses: {
                    info: 'bg-gradient-to-l from-navy-900 to-navy-800',
                    warning: 'bg-gradient-to-l from-amber-600 to-amber-500',
                    urgent: 'bg-gradient-to-l from-rose-700 to-rose-600',
                },
                get visible() { return this.all.filter(a => !this.dismissed.includes(a.id)); },
                get current() { return this.visible[this.index] || null; },
                init() { if (this.visible.length > 1) this.start(); },
                start() { this.timer = setInterval(() => { this.index = (this.index + 1) % this.visible.length; }, 6000); },
                restart() { clearInterval(this.timer); if (this.visible.length > 1) this.start(); },
                dismiss() {
                    if (!this.current) return;
                    this.dismissed.push(this.current.id);
                    localStorage.setItem('dismissedAnnouncements', JSON.stringify(this.dismissed));
                    if (this.index >= this.visible.length) this.index = 0;
                    this.restart();
                },
             }">
            <div class="rounded-2xl shadow-sm px-5 py-3.5 flex items-center gap-4" :class="typeClasses[current.type] || typeClasses.info">
                <span class="text-2xl shrink-0" x-text="current.icon"></span>
                <div class="flex-1 min-w-0">
                    <h3 class="font-black text-white text-sm leading-tight" x-text="current.title"></h3>
                    <p class="text-white/75 text-xs font-bold" x-text="current.body"></p>
                </div>
                <template x-if="visible.length > 1">
                    <div class="hidden sm:flex items-center gap-1.5 shrink-0">
                        <template x-for="(item, i) in visible" :key="item.id">
                            <button type="button" @click="index = i; restart()" class="h-1.5 rounded-full transition-all" :class="i === index ? 'w-5 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/60'" aria-label="إعلان آخر"></button>
                        </template>
                    </div>
                </template>
                <button type="button" @click="dismiss()" class="shrink-0 text-white/70 hover:text-white hover:bg-white/10 rounded-lg p-1.5 transition" aria-label="إخفاء الإعلان">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-[2.5rem] p-8 mb-10 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-3xl font-black text-gold-600">{{ $profileCompletion }}%</span>
                        <h2 class="font-black text-slate-800 text-xl">أكمل ملفك الشخصي</h2>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full">
                        <div class="bg-gold-600 h-full rounded-full transition-all duration-1000" style="width: {{ $profileCompletion }}%"></div>
                    </div>
                </div>
                <a href="{{ route('dashboard.profile') }}" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black hover:bg-slate-800 transition shadow-lg">إكمال الملف الشخصي</a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
            @php
                $labels = [
                    'favorites' => 'المنح المحفوظة',
                    'applications' => 'الطلبات النشطة',
                    'completed' => 'الطلبات المكتملة',
                    'review' => 'قيد المراجعة'
                ];
                $colors = [
                    'favorites' => 'bg-blue-50 text-blue-600',
                    'applications' => 'bg-gold-100 text-gold-600',
                    'completed' => 'bg-green-50 text-green-600',
                    'review' => 'bg-orange-50 text-orange-600'
                ];
            @endphp
            @foreach($stats as $key => $count)
            <div class="bg-white p-6 rounded-[2rem] border border-slate-50 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 {{ $colors[$key] ?? 'bg-slate-50 text-slate-600' }} rounded-2xl flex items-center justify-center mb-4 font-black text-xl">
                    {{ $count }}
                </div>
                <p class="text-slate-400 font-bold text-sm">{{ $labels[$key] ?? '' }}</p>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-slate-800">منح موصى بها لك</h3>
                    <a href="{{ route('dashboard.scholarships') }}" class="text-gold-600 font-bold text-sm">عرض الكل ←</a>
                </div>

                <div class="space-y-6">
                    @forelse($recommended_scholarships as $scholarship)
                    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-50 shadow-sm flex flex-col md:flex-row items-center gap-6 hover:border-gold-100 transition">
                        
                        <div class="w-16 h-16 flex-shrink-0 rounded-full overflow-hidden bg-slate-100 border border-slate-200/60 shadow-sm flex items-center justify-center">
                            @if($scholarship->logo_image)
                                <img src="{{ $scholarship->logo_image }}" 
                                     alt="Logo" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-50 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 text-center md:text-right">
                            <h4 class="font-black text-slate-800 mb-1">{{ $scholarship->title_ar }}</h4>
                            <p class="text-xs text-slate-400 font-bold mb-3">{{ $scholarship->country }}</p>
                            <div class="flex flex-wrap justify-center md:justify-start gap-2">
                                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ $scholarship->value ?? '₪50,000' }}</span>
                                <span class="bg-gold-100 text-gold-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ $scholarship->tags[0] ?? 'ممولة بالكامل' }}</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('dashboard.scholarships.show', $scholarship->id) }}" class="bg-gold-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-gold-700 transition w-full md:w-auto text-center block">عرض التفاصيل</a>
                    </div>
                    @empty
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-50 shadow-sm text-center">
                        <p class="text-slate-400 font-bold">لا توجد منح متاحة حالياً</p>
                    </div>
                    @endforelse
                </div>

                <h3 class="text-xl font-black text-slate-800 mt-12 mb-6">النشاط الأخير</h3>
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50 space-y-8">
                    @forelse($activities as $activity)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 {{ $activity['type'] === 'completed' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }} rounded-full flex items-center justify-center flex-shrink-0">
                            {{ $activity['type'] === 'completed' ? '✓' : 'ℹ' }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800">
                                {{ $activity['type'] === 'completed' ? 'تم إكمال طلبك' : 'تم تقديم طلب جديد' }}
                            </p>
                            <p class="text-xs text-slate-400">{{ $activity['title'] }} • {{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center">
                        <p class="text-slate-400 font-bold text-sm">لا يوجد نشاط سابق</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-10">
                
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-50">
                    <h3 class="font-black text-slate-800 mb-6">قائمة المهام</h3>
                    <div class="space-y-4">
                        @forelse($tasks as $task)
                            <a href="{{ $task['completed'] ? '#' : $task['link'] }}" class="flex items-center gap-4 group select-none {{ $task['completed'] ? 'cursor-default' : 'cursor-pointer' }}">
                                <span class="w-6 h-6 rounded-lg border-2 flex items-center justify-center flex-shrink-0 {{ $task['completed'] ? 'bg-gold-600 border-gold-600 text-white' : 'border-slate-200' }}">
                                    @if($task['completed'])
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </span>
                                <span class="text-sm font-bold transition-all duration-300 {{ $task['completed'] ? 'text-slate-300 line-through' : 'text-slate-600 group-hover:text-gold-600' }}">{{ $task['title'] }}</span>
                            </a>
                        @empty
                            <div class="text-slate-400 text-xs font-bold text-center py-4">
                                📭 لا توجد مهام حالية المطلوبة منك.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-50">
                    <h3 class="font-black text-slate-800 mb-6">الإنجازات المكتسبة</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-center">
                        @foreach($badges as $badge)
                            <div class="w-full aspect-square bg-slate-50 rounded-2xl flex flex-col items-center justify-center p-2 cursor-help transition-all duration-300 transform hover:scale-105 border border-transparent {{ $badge['unlocked'] ? 'border-amber-100 bg-amber-50/20' : 'grayscale opacity-40' }}"
                                 title="{{ ($badge['unlocked'] ? 'إنجاز مكتمل: ' : 'لم يتم فتح الإنجاز بعد: ') . $badge['description'] }}">
                                <span class="text-3xl mb-1">{{ $badge['icon'] }}</span>
                                <span class="text-[9px] font-black tracking-tight text-slate-500 block truncate w-full">{{ $badge['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-gold-500 via-gold-600 to-amber-600 p-7 rounded-[2.5rem] shadow-xl shadow-gold-200/50"
                     x-data="{ copied: false, showInfo: false, shareUrl: '{{ url('/register?ref=' . auth()->id()) }}' }">
                    <div class="absolute -top-8 -left-8 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-10 -right-6 w-28 h-28 bg-white/10 rounded-full"></div>

                    <div class="relative flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-2xl animate-bounce" style="animation-duration: 2.5s;">
                            🎁
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-white text-base">ادعُ أصدقاءك واربح نقاط XP</h4>
                            <p class="text-[11px] text-gold-50 font-bold mt-0.5">كل صديق يسجل من رابطك = <span class="font-black">+25 XP</span> فوراً لك</p>
                        </div>
                        <button type="button" @click="showInfo = !showInfo" title="كيف تعمل نقاط XP؟"
                                class="w-7 h-7 shrink-0 rounded-full bg-white/20 hover:bg-white/30 text-white font-black text-xs flex items-center justify-center transition">؟</button>
                    </div>

                    <div class="relative flex items-center gap-2 bg-white/15 backdrop-blur p-1.5 rounded-2xl border border-white/20 overflow-hidden">
                        <input type="text" readonly :value="shareUrl" class="bg-transparent border-0 text-xs font-bold px-2 text-white placeholder-white/70 focus:ring-0 flex-1 min-w-0 truncate select-all" dir="ltr">
                        <button @click="
                                navigator.clipboard.writeText(shareUrl);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                                if (window.confetti) { window.confetti({ particleCount: 70, spread: 65, origin: { y: 0.7 }, colors: ['#f5c518','#ffffff','#1a2942'] }); }
                            "
                                :class="copied ? 'bg-emerald-500 text-white' : 'bg-white text-gold-700 hover:bg-gold-50'"
                                class="px-4 py-2.5 rounded-xl font-black text-xs transition duration-300 flex-shrink-0 shadow-sm">
                            <span x-text="copied ? '✅ تم النسخ!' : 'نسخ الرابط'"></span>
                        </button>
                    </div>

                    <div x-show="showInfo" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="relative mt-4 bg-white/95 rounded-2xl p-4 space-y-3 text-right">
                        <p class="text-[11px] font-black text-slate-800 mb-1">كيف تجمع نقاط XP؟</p>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-gold-100 text-gold-600 flex items-center justify-center text-sm font-black shrink-0">🤝</span>
                            <p class="text-[11px] font-bold text-slate-600">+25 XP عن كل صديق يسجل من رابط دعوتك</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-black shrink-0">⏱️</span>
                            <p class="text-[11px] font-bold text-slate-600">+25 XP عن كل ساعة تقضيها متصفحاً الموقع</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-black shrink-0">🏆</span>
                            <p class="text-[11px] font-bold text-slate-600">كل 1000 نقطة = تقديم مجاني على منحة من فريقنا نيابةً عنك</p>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 pt-1 border-t border-slate-100">رصيدك الحالي: <span class="text-gold-600 font-black">{{ auth()->user()->xp }} XP</span></p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gold-500 to-gold-600 p-8 rounded-[2.5rem] text-white text-center shadow-lg shadow-gold-100">
                    <h4 class="font-black mb-4">تحتاج مساعدة؟</h4>
                    <p class="text-xs opacity-80 mb-6 leading-relaxed">المساعد الذكي 🤖 متاح دائماً أسفل الشاشة للإجابة على استفساراتك فوراً</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection