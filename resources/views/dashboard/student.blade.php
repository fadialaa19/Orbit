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

                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100" x-data="{ copied: false, shareUrl: '{{ url('/register?ref=' . auth()->id()) }}' }">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gold-100 rounded-xl flex items-center justify-center text-xl">
                            🎁
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-sm">ادعُ أصدقاءك للمنصة</h4>
                            <p class="text-[11px] text-slate-400 font-bold mt-0.5">واكسب <span class="text-gold-600 font-black">+250 XP</span> عن كل تسجيل!</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                        <input type="text" readonly :value="shareUrl" class="bg-transparent border-0 text-xs font-medium px-2 text-slate-500 focus:ring-0 flex-1 truncate select-all" dir="ltr">
                        <button @click="navigator.clipboard.writeText(shareUrl); copied = true; setTimeout(() => copied = false, 2000)" 
                                :class="copied ? 'bg-green-600 text-white' : 'bg-slate-900 text-white hover:bg-slate-800'"
                                class="px-4 py-2.5 rounded-xl font-bold text-xs transition duration-300 flex-shrink-0 shadow-sm">
                            <span x-text="copied ? 'تم النسخ!  ' : 'نسخ الرابط'"></span>
                        </button>
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