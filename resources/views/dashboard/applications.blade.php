@extends('layouts.dashboard')

@section('title', 'طلباتِي')

@section('header_search', '')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 px-4 md:px-10" dir="rtl">
    <div class="max-w-5xl mx-auto">
        <div class="mb-10 text-right">
            <h1 class="text-3xl font-black text-slate-800 mb-2">تتبّع الطّلبات</h1>
            <p class="text-slate-400 font-bold italic">راقب حالة طلبات المنح الدراسية الخاصة بك</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            @php
                $total = $stats['total'] ?? 0;
                $pending = $stats['pending'] ?? 0;
                $processing = $stats['processing'] ?? 0;
                $approved = $stats['approved'] ?? 0;
                $rejected = $stats['rejected'] ?? 0;
            @endphp

            @foreach([
                ['إجمالي الطلبات', $total, '📄', 'text-indigo-600'],
                ['قيد الانتظار', $pending, '🕒', 'text-purple-600'],
                ['مقبولة', $approved, '✅', 'text-green-600'],
                ['مرفوضة', $rejected, '❌', 'text-red-600'],
            ] as $stat)
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50 flex flex-col items-center">
                    <span class="text-2xl mb-2">{{ $stat[2] }}</span>
                    <span class="text-2xl font-black {{ $stat[3] }}">{{ $stat[1] }}</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $stat[0] }}</span>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            @forelse($applications as $application)
                @php
                    $status = $application->status;

                    // Badge
                    $badge = [
                        'class' => 'bg-slate-50 text-slate-600',
                        'label' => $status,
                    ];

                    // Progress
                    $progress = [
                        'percent' => 0,
                        'barClass' => 'bg-indigo-600',
                    ];

                    // Steps
                    $stepTitles = [
                        'تقديم الطلب',
                        'قيد المراجعة',
                        'تقييم المستندات',
                        'المقبولية',
                        'القرار النهائي',
                    ];

                    $stepsText = [];

                    if ($status === 'pending') {
                        $badge = ['class' => 'bg-yellow-50 text-yellow-700', 'label' => 'قيد الانتظار'];
                        $progress = ['percent' => 20, 'barClass' => 'bg-yellow-500'];
                        $stepsText = ['تم', 'في الطريق', 'بداية المراجعة', 'قريبا', '—'];
                    } elseif ($status === 'processing') {
                        $badge = ['class' => 'bg-purple-50 text-purple-700', 'label' => 'قيد المراجعة'];
                        $progress = ['percent' => 60, 'barClass' => 'bg-purple-600'];
                        $stepsText = ['تم', 'يتم فحص', 'جارٍ التقييم', 'قريبا', '—'];
                    } elseif ($status === 'approved') {
                        $badge = ['class' => 'bg-green-50 text-green-700', 'label' => 'مقبول'];
                        $progress = ['percent' => 100, 'barClass' => 'bg-green-600'];
                        $stepsText = ['تم', 'مراجعة مكتملة', 'تم التقييم', 'مقبول', 'منتهي'];
                    } elseif ($status === 'rejected') {
                        $badge = ['class' => 'bg-red-50 text-red-700', 'label' => 'مرفوض'];
                        $progress = ['percent' => 100, 'barClass' => 'bg-red-600'];
                        $stepsText = ['تم', 'مراجعة', 'انتهت', 'ملغي', 'ملغي'];
                    }

                    $scholarshipTitle = $application->scholarship->title_ar ?? '—';
                @endphp

                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                        <div class="text-right">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-xl font-black text-slate-800">{{ $scholarshipTitle }}</h3>
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black italic {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>

                            <p class="text-slate-400 font-bold text-xs">
                                تم التقديم في:
                                {{ optional($application->created_at)->translatedFormat('d M Y') ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden mb-4">
                            <div
                                class="h-full {{ $progress['barClass'] }} transition-all duration-700"
                                style="width: {{ $progress['percent'] }}%"
                            ></div>
                        </div>
                        <p class="text-[10px] font-black text-slate-500">نسبة التقدم: {{ $progress['percent'] }}%</p>
                    </div>

                    <div class="grid grid-cols-5 gap-2">
                        @for($i = 0; $i < 5; $i++)
                            @php
                                $stepCompleted = false;

                                if ($status === 'pending') {
                                    $stepCompleted = $i <= 0;
                                } elseif ($status === 'processing') {
                                    $stepCompleted = $i <= 2;
                                } else {
                                    $stepCompleted = true;
                                }

                                $stepBg = $stepCompleted ? $progress['barClass'] : 'bg-slate-200';
                            @endphp

                            <div class="text-center">
                                <div class="w-8 h-8 mx-auto mb-2 rounded-full flex items-center justify-center text-white text-[10px] font-black {{ $stepBg }}">
                                    {{ $i + 1 }}
                                </div>
                                <p class="text-[9px] font-black text-slate-500">{{ $stepTitles[$i] }}</p>
                                <p class="text-[8px] font-bold text-slate-400 italic">{{ $stepsText[$i] ?? '' }}</p>
                            </div>
                        @endfor
                    </div>

                    @if($status === 'approved' && !empty($application->admission_letter_path))
                        <div class="mt-6">
                            <a
                                href="{{ asset('storage/' . $application->admission_letter_path) }}"
                                target="_blank"
                                class="bg-green-600 text-white px-6 py-2 rounded-xl text-xs font-black shadow-lg shadow-green-100 hover:bg-green-700 transition inline-flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                                تحميل خطاب القبول
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-50 text-center">
                    <p class="text-slate-400 font-bold">لا توجد طلبات حالية بعد</p>
                    <p class="text-slate-500 text-sm font-bold mt-2">ابدأ باستعراض المنح المتاحة وقدّم طلبك الآن.</p>
                    <div class="mt-6">
                        <a
                            href="{{ route('dashboard.scholarships') }}"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition inline-flex items-center gap-2"
                        >
                            استعرض المنح المتاحة
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

