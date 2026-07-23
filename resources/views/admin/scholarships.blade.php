@extends('layouts.admin')

@section('title', 'إدارة المنح')
@section('breadcrumb', 'المنح الدراسية')

@section('content')

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<div class="max-w-full mx-auto space-y-6" x-data="{ activeTab: '{{ request()->routeIs('*.create') ? 'builder' : 'list' }}' }">

    @if(session('success'))
        <div class="bg-emerald-500 text-white p-4 rounded-2xl font-black text-sm shadow-lg shadow-emerald-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-50">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <span class="text-gold-500">🎓</span>  إدارة المنح الدراسية
            </h1>
            <p class="text-xs font-bold text-slate-400 mt-1">قم بنشر منح جديدة وتعديلها أو توليد الأقسام بالذكاء الاصطناعي</p>
        </div>

        <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-100 w-full lg:w-auto">
            <button @click="activeTab = 'list'" :class="activeTab === 'list' ? 'bg-white text-gold-600 shadow-sm font-black' : 'text-slate-400 font-bold hover:text-slate-600'" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                📋 قائمة المنح الحالية
            </button>
            <button @click="activeTab = 'builder'" :class="activeTab === 'builder' ? 'bg-white text-gold-600 shadow-sm font-black' : 'text-slate-400 font-bold hover:text-slate-600'" class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                ✨ منشئ المنح الذكي
            </button>
        </div>
    </div>

    <div x-show="activeTab === 'list'" x-transition class="bg-white rounded-[2rem] shadow-sm border border-slate-50 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-700">قائمة المنح المنشورة بالموقع</h2>
            <span class="bg-slate-50 border text-slate-500 font-black text-[10px] px-3 py-1 rounded-full">العدد الإجمالي: {{ $stats['total'] }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-50 text-[11px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="p-5">المنحة والجامعة</th>
                        <th class="p-5">الدولة</th>
                        <th class="p-5">المرحلة</th>
                        <th class="p-5">القيمة المالية</th>
                        <th class="p-5">الموعد النهائي</th>
                        <th class="p-5">الحالة</th>
                        <th class="p-5 text-left">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-bold text-slate-600">
                    @forelse($scholarships as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                @if($item->logo_image)
                                    <img src="{{ $item->logo_image }}" class="w-10 h-10 rounded-xl object-contain border bg-white p-1">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gold-100 text-gold-500 font-black flex items-center justify-center text-xs">🎓</div>
                                @endif
                                <div>
                                    <div class="font-black text-slate-800 text-sm">{{ $item->title_ar }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $item->university }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-xs font-black text-slate-500">{{ $item->country }}</td>
                        <td class="p-5">
                            <span class="px-3 py-1 bg-slate-100 rounded-lg text-[11px] font-black text-slate-600">{{ $item->category_label }}</span>
                        </td>
                        <td class="p-5 text-xs font-black text-gold-600">{{ $item->financial_value ?? 'غير محدد' }}</td>
                        <td class="p-5 text-xs text-slate-500">{{ $item->deadline }}</td>
                        <td class="p-5">
                            @if(($item->status ?? 'active') == 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100">● نشط</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-100">● مغلق</span>
                            @endif
                        </td>
                        <td class="p-5 text-left">
                            <div class="flex items-center justify-end gap-2" x-data="{ copied: false }">
                                <button type="button" @click="navigator.clipboard.writeText('{{ route('guest.scholarships.show', $item) }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                    class="p-2 bg-slate-50 hover:bg-navy-100 hover:text-navy-700 text-slate-400 rounded-xl border transition-all" title="نسخ رابط صفحة المنحة">
                                    <span x-show="!copied">🔗</span>
                                    <span x-show="copied" x-cloak class="text-emerald-600">✅</span>
                                </button>
                                <a href="{{ route('admin.scholarships.edit', $item) }}" class="p-2 bg-slate-50 hover:bg-gold-100 hover:text-gold-600 text-slate-400 rounded-xl border transition-all">✏️</a>
                                <form action="{{ route('admin.scholarships.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المنحة نهائياً؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-slate-50 hover:bg-rose-50 hover:text-rose-600 text-slate-400 rounded-xl border transition-all">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center font-bold text-slate-400 text-xs">لا يوجد أي منح دراسية مضافة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($scholarships->hasPages())
        <div class="p-5 border-t border-slate-50 bg-slate-50/50">
            {{ $scholarships->links() }}
        </div>
        @endif
    </div>

    <div x-show="activeTab === 'builder'" x-transition class="bg-white rounded-[2rem] shadow-sm border border-slate-50 overflow-hidden">
        <form action="{{ route('admin.scholarships.store') }}" method="POST" id="scholarshipForm" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 divide-y lg:divide-y-0 lg:divide-x lg:divide-x-reverse divide-slate-100">
                
                <div class="lg:col-span-8 p-8 space-y-8">

                    <div class="bg-gradient-to-br from-navy-900 to-navy-800 rounded-[1.5rem] p-6 shadow-lg space-y-4">
                        <h3 class="text-sm font-black text-white flex items-center gap-2">
                            <span class="text-gold-400">🤖</span> تعبئة تلقائية من نص خام بالذكاء الاصطناعي
                        </h3>
                        <p class="text-xs text-slate-300 font-bold leading-relaxed">
                            الصق أي نص عن المنحة (من موقع الجامعة، ملف، أو رسالة) وخلي الذكاء الاصطناعي يقرأه ويوزّع بياناته على كل حقول النموذج تلقائياً، بما فيها العنوان والجامعة والموعد النهائي والأقسام النصية.
                        </p>
                        <textarea id="raw_text_input" rows="6" placeholder="الصق النص الخام هنا..."
                                  class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-sm font-bold text-white placeholder-slate-400 outline-none focus:border-gold-400 transition-all resize-y"></textarea>
                        <button type="button" onclick="autofillFromRawText()" id="autofillBtn"
                                class="w-full bg-gold-500 text-white font-black py-3.5 rounded-2xl text-xs hover:bg-gold-400 transition-all shadow-lg shadow-gold-500/20 flex items-center justify-center gap-2">
                            <svg id="autofillIcon" class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span id="autofillText">تعبئة كل الحقول تلقائياً</span>
                        </button>
                    </div>

                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-6">
                        <h3 class="text-sm font-black text-slate-800 pb-3 border-b border-slate-50 flex items-center gap-2">
                            <span class="text-gold-500">📝</span> البيانات الأساسية للمنحة
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">عنوان المنحة (بالعربية)</label>
                                <div class="relative flex gap-2">
                                    <input type="text" name="title_ar" id="title_ar" required placeholder="مثال: منحة جامعة اكسفورد للتميز الدراسي والبحث العلمي" class="flex-1 bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                                    <button type="button" onclick="generateAllSections()" id="aiGenerateBtn" class="bg-amber-100 text-amber-700 px-4 py-2 rounded-xl font-black text-[10px] uppercase flex items-center gap-2 hover:bg-amber-200 transition-all border border-amber-200 shrink-0">
                                        <svg id="aiIcon" class="w-4 h-4 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span id="aiText">توليد الأقسام ذكياً</span>
                                    </button>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Scholarship Title (English)</label>
                                <input type="text" name="title_en" required placeholder="Example: Oxford University Excellence Scholarship for International Students" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all text-left">
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">القيمة المالية والتمويل (للإظهار في الكارد)</label>
                                <input type="text" name="financial_value" placeholder="مثال: تمويل كامل يشمل الرسوم، السكن، وراتب شهري 1200$" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            </div>

                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">عدد المتقدمين الافتراضي</label>
                                <input type="number" name="applicants_count" value="0" min="0" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            </div>

                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الحد الأدنى للمعدل (اختياري)</label>
                                <input type="number" name="min_gpa" step="0.1" min="0" max="100" placeholder="مثال: 80" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                                <p class="text-[10px] text-slate-400 mt-1">بيُستخدم لحساب نسبة توافق الطالب بدقة - سيبه فاضي لو مفيش حد أدنى محدد</p>
                            </div>

                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الكلمات المفتاحية الموصى بها لكارد المنحة</label>
                                <input type="text" name="recommended_tags" placeholder="ممولة كاملاً, هندسة, بدون آيلتس" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                                <p class="text-[10px] text-slate-400 mt-1">افصل بين الكلمات بفاصلة لسهولة القراءة</p>
                            </div>

                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الدولة</label>
                                <input type="text" name="country" id="country" required placeholder="المملكة المتحدة" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            </div>
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الجامعة المانحة</label>
                                <input type="text" name="university" id="university" required placeholder="جامعة أكسفورد - Oxford" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الموعد النهائي للتقديم</label>
                                <input type="date" name="deadline" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            </div>
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">التصنيف والمرحلة الدراسية (يمكن اختيار أكثر من مرحلة)</label>
                                <div class="grid grid-cols-2 gap-2" id="category-checkboxes">
                                    @foreach(['Bachelor' => 'بكالوريوس (Undergraduate)', 'Master' => 'ماجستير (Postgraduate Master)', 'PhD' => 'دكتوراه (PhD / Doctoral)', 'Short Course' => 'كورسات قصيرة وزمالات'] as $key => $label)
                                    <label class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs font-bold cursor-pointer hover:border-gold-300 transition-all has-[:checked]:border-gold-400 has-[:checked]:bg-gold-50">
                                        <input type="checkbox" name="categories[]" value="{{ $key }}" class="category-checkbox w-4 h-4 rounded text-gold-600 focus:ring-gold-500" {{ $key === 'Bachelor' ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">رابط التقديم المباشر (خارجي)</label>
                            <input type="url" name="application_url" placeholder="https://www.ox.ac.uk/admissions/apply" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all text-left">
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">رابط "التقديم عن طريقنا" (واتساب / تيليجرام)</label>
                            <input type="url" name="apply_via_us_link" placeholder="https://wa.me/9705xxxxxxx" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all text-left">
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">
                                <span class="p-1.5 bg-gold-100 text-gold-500 rounded-lg text-xs">📝</span> الوصف العام والمقدمة للمنحة
                            </label>
                            <input type="hidden" name="description" id="description_input">
                            <div class="editor-container"><div id="description_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">
                                <span class="p-1.5 bg-sky-50 text-sky-500 rounded-lg text-xs">📖</span> نظرة عامة عن التمويل والفرصة
                            </label>
                            <input type="hidden" name="overview" id="overview_input">
                            <div class="editor-container"><div id="overview_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">
                                <span class="p-1.5 bg-emerald-50 text-emerald-500 rounded-lg text-xs">✅</span> شروط الأهلية ومعايير القبول
                            </label>
                            <input type="hidden" name="conditions" id="conditions_input">
                            <div class="editor-container"><div id="conditions_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">
                                <span class="p-1.5 bg-amber-50 text-amber-500 rounded-lg text-xs">📄</span> الوثائق والمستندات المطلوبة للتقديم
                            </label>
                            <input type="hidden" name="documents" id="documents_input">
                            <div class="editor-container"><div id="documents_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">
                                <span class="p-1.5 bg-rose-50 text-rose-500 rounded-lg text-xs">⭐</span> المزايا والفوائد المالية التي توفرها المنحة
                            </label>
                            <input type="hidden" name="features" id="features_input">
                            <div class="editor-container"><div id="features_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">
                                <span class="p-1.5 bg-emerald-50 text-emerald-500 rounded-lg text-xs">🧭</span> آلية التقديم على المنحة خطوة بخطوة
                            </label>
                            <input type="hidden" name="application_process" id="application_process_input">
                            <div class="editor-container"><div id="application_process_editor" class="quill-editor"></div></div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 p-8 space-y-8 bg-slate-50/40">
                    
                    <div class="p-6 bg-slate-900 rounded-[2rem] shadow-xl text-white space-y-4">
                        <div class="flex items-center gap-2 text-xs font-black text-gold-300 uppercase tracking-widest">
                            ⚡ لوحة التحكم بالمنحة
                        </div>
                        <p class="text-xs text-slate-400 font-bold">تأكد من مراجعة وتوليد الحقول والأقسام قبل الضغط على نشر وتعميم المنحة في الموقع.</p>
                        <button type="submit" class="w-full bg-gold-500 text-white font-black py-4 rounded-2xl text-xs hover:bg-gold-400 transition-all shadow-lg shadow-gold-500/20">
                            🚀 نشر المنحة بالموقع الآن
                        </button>
                    </div>

                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-5">
                        <h4 class="text-xs font-black text-slate-700 flex items-center gap-2">🖼️ صور وبوستر المنحة</h4>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">الصورة الرئيسية (بوستر المنحة - للابتوب)</label>
                            <input type="file" id="main_image_input" name="main_image" accept="image/*" class="w-full text-xs font-bold text-slate-500 file:ml-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <p class="text-[9px] text-slate-400">سيتم تصغير الصورة تلقائياً لتناسب حجم الموقع (١٦٠٠×٣٢٠)</p>
                            <input type="url" name="main_image_url" placeholder="أو الصق رابط صورة مباشر (https://...)" dir="ltr"
                                   class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-slate-700 focus:border-gold-300 outline-none transition">
                        </div>

                        <div class="space-y-2 pt-2 border-t border-slate-50">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">صورة مخصصة للجوال (اختياري)</label>
                            <input type="file" id="main_image_mobile_input" name="main_image_mobile" accept="image/*" class="w-full text-xs font-bold text-slate-500 file:ml-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <p class="text-[9px] text-slate-400">سيتم تصغير الصورة تلقائياً لمقاس الجوال (٨٠٠×٤٠٠) - لو ما رفعت شي هون، بتنعرض صورة اللابتوب كاملة بدون قص على الجوال</p>
                            <input type="url" name="main_image_mobile_url" placeholder="أو الصق رابط صورة مباشر (https://...)" dir="ltr"
                                   class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-slate-700 focus:border-gold-300 outline-none transition">
                        </div>

                        <div class="space-y-2 pt-2 border-t border-slate-50">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">شعار الجامعة أو الجهة المانحة</label>
                            <input type="file" name="logo_image" accept="image/*" class="w-full text-xs font-bold text-slate-500 file:ml-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <input type="url" name="logo_image_url" placeholder="أو الصق رابط صورة مباشر (https://...)" dir="ltr"
                                   class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-xs font-bold text-slate-700 focus:border-gold-300 outline-none transition">
                        </div>
                        <p class="text-[10px] text-slate-400">رفع ملف له الأولوية على الرابط لو وُجد الاثنين معاً</p>
                    </div>

                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-4">
                        <h4 class="text-xs font-black text-slate-700 flex items-center gap-2">🏷️ التخصصات المتاحة (فلاتر Tags)</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['هندسة', 'طب', 'تقنية', 'إدارة', 'علوم', 'فن'] as $tag)
                            <label class="group cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag }}" class="hidden peer">
                                <span class="px-4 py-2 bg-slate-50 text-slate-500 rounded-xl text-[10px] font-black peer-checked:bg-gold-600 peer-checked:text-white transition-all border border-slate-100 inline-block">
                                    {{ $tag }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-4">
                        <h4 class="text-xs font-black text-slate-700 flex items-center gap-2">💰 فلاتر التغطية والتمويل (Coverage)</h4>
                        <div class="space-y-3">
                            @foreach(['تمويل كامل', 'إعفاء من الرسوم', 'راتب شهري', 'تأمين صحي', 'تذاكر طيران', 'سكن جامعي'] as $coverage)
                            <label class="flex items-center gap-3 group cursor-pointer">
                                <input type="checkbox" name="coverage[]" value="{{ $coverage }}" class="w-5 h-5 rounded-lg border-slate-200 text-gold-600 focus:ring-gold-500 transition-all">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-slate-800 transition-colors">{{ $coverage }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* تنسيقات مخصصة ومحسنة لمحرر الـ Quill ليظهر بمظهر الـ Word والاحترافي */
    .word-editor-card { display: flex; flex-direction: column; }
    .word-editor-card .editor-container { position: relative; width: 100%; }
    .word-editor-card .ql-toolbar.ql-snow { border: 1px solid rgba(15, 23, 42, 0.06) !important; background: #f8fafc !important; border-top-left-radius: 0.75rem !important; border-top-right-radius: 0.75rem !important; text-align: right; direction: rtl; padding: 10px !important; }
    .word-editor-card .ql-container.ql-snow { border: 1px solid rgba(15, 23, 42, 0.06) !important; border-top: none !important; border-bottom-left-radius: 0.75rem !important; border-bottom-right-radius: 0.75rem !important; min-height: 180px; max-height: 400px; overflow-y: auto; background: #ffffff; }
    .word-editor-card .ql-editor { direction: rtl; text-align: right; font-size: 0.9rem; font-weight: 600; line-height: 1.7; color: #334155; padding: 16px !important; }
    .word-editor-card .ql-editor.ql-blank::before { left: auto !important; right: 16px !important; text-align: right; direction: rtl; color: #cbd5e1; font-style: normal; }
    .word-editor-card .ql-editor img { max-width: 100%; height: auto; border-radius: 0.5rem; }
</style>

<script>
const editors = {};

function initAllEditors() {
    const fields = ['description', 'overview', 'conditions', 'documents', 'features', 'application_process'];
    
    const toolbarOptions = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['link', 'image'],
        ['clean']
    ];

    fields.forEach(field => {
        const containerSelector = `#${field}_editor`;
        const inputSelector = `#${field}_input`;

        if (document.querySelector(containerSelector)) {
            editors[field] = new Quill(containerSelector, {
                theme: 'snow',
                dir: 'rtl',
                placeholder: `أدخل تفاصيل وعناصر حقل ${field === 'overview' ? 'النظرة العامة' : field}...`,
                modules: { toolbar: { container: toolbarOptions, handlers: { image: () => uploadEditorImage(field) } } }
            });

            // مزامنة فورية عند الكتابة والتعديل إلى الـ input المخفي
            editors[field].on('text-change', () => {
                document.querySelector(inputSelector).value = editors[field].root.innerHTML;
            });
        }
    });
}

// رفع صورة من داخل المحرر وإدراجها عند موضع المؤشر بالضبط (تحكم كامل بمكان الصورة)
function uploadEditorImage(field) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async () => {
        const file = input.files[0];
        if (!file) return;

        const editor = editors[field];
        const range = editor.getSelection(true);

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch("{{ route('admin.scholarships.rich-text.upload-image') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: formData
            });
            const data = await response.json();
            if (response.ok && data.url) {
                editor.insertEmbed(range.index, 'image', data.url, 'user');
                editor.setSelection(range.index + 1);
                document.querySelector(`#${field}_input`).value = editor.root.innerHTML;
            } else {
                alert(data.message || 'تعذّر رفع الصورة، الرجاء المحاولة مجدداً.');
            }
        } catch (error) {
            console.error('Image upload error:', error);
            alert('حدث خطأ أثناء رفع الصورة، تحقق من الاتصال بالشبكة.');
        }
    };
    input.click();
}

function setQuillHtml(field, html) {
    if (editors[field]) {
        editors[field].root.innerHTML = html;
        document.querySelector(`#${field}_input`).value = html;
    }
}

async function generateAllSections() {
    const title = document.getElementById('title_ar').value.trim();
    const university = document.getElementById('university').value.trim();
    const country = document.getElementById('country').value.trim();
    const checkedCategory = document.querySelector('.category-checkbox:checked');
    const category = checkedCategory ? checkedCategory.value : 'Bachelor';

    if (!title || !university || !country) {
        alert('من فضلك املأ حقول العنوان (بالعربية)، الجامعة، والدولة أولاً لتوليد البيانات ذكياً.');
        return;
    }

    const aiBtn = document.getElementById('aiGenerateBtn');
    const aiText = document.getElementById('aiText');
    
    aiBtn.disabled = true;
    aiBtn.classList.add('opacity-60', 'cursor-not-allowed');
    aiText.innerText = 'جاري التوليد ذكياً...';

    try {
        const response = await fetch("{{ route('admin.scholarships.generate-sections') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                title_ar: title,
                university: university,
                country: country,
                category: category
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // تم التعديل هنا ليقرأ من كائن data.data مباشرة والمُرسل من الكنترولر المعدل
            setQuillHtml('description', data.data.description || '');
            setQuillHtml('overview', data.data.overview || '');
            setQuillHtml('conditions', data.data.conditions || '');
            setQuillHtml('documents', data.data.documents || '');
            setQuillHtml('features', data.data.features || '');
            setQuillHtml('application_process', data.data.application_process || '');

            aiText.innerText = '✅ تم توليد الأقسام!';
            setTimeout(() => {
                aiText.innerText = 'توليد الأقسام ذكياً';
            }, 3000);
        } else {
            alert(data.error || 'حدث خطأ غير متوقع أثناء توليد الأقسام.');
            aiText.innerText = 'توليد الأقسام ذكياً';
        }
    } catch (error) {
        console.error('AI Error:', error);
        alert('حدث خطأ في الاتصال بالخادم، تأكد من وجود الـ CSRF Token وحالة الشبكة.');
        aiText.innerText = 'توليد الأقسام ذكياً';
    } finally {
        aiBtn.disabled = false;
        aiBtn.classList.remove('opacity-60', 'cursor-not-allowed');
    }
}

async function autofillFromRawText() {
    const rawText = document.getElementById('raw_text_input').value.trim();
    if (!rawText) {
        alert('الصق نص المنحة الخام أولاً.');
        return;
    }

    const btn = document.getElementById('autofillBtn');
    const label = document.getElementById('autofillText');

    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-not-allowed');
    label.innerText = 'جاري القراءة والتحليل...';

    try {
        const response = await fetch("{{ route('admin.scholarships.ai-autofill-text') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ raw_text: rawText })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            applyAutofillData(result.data);
            label.innerText = '✅ تم تعبئة الحقول!';
            setTimeout(() => { label.innerText = 'تعبئة كل الحقول تلقائياً'; }, 3000);
        } else {
            alert(result.error || 'حدث خطأ غير متوقع أثناء التحليل.');
            label.innerText = 'تعبئة كل الحقول تلقائياً';
        }
    } catch (error) {
        console.error('AI Autofill Error:', error);
        alert('حدث خطأ في الاتصال بالخادم، تأكد من الاتصال بالشبكة.');
        label.innerText = 'تعبئة كل الحقول تلقائياً';
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
    }
}

function applyAutofillData(data) {
    const simpleFields = ['title_ar', 'title_en', 'country', 'university', 'financial_value',
        'applicants_count', 'min_gpa', 'recommended_tags', 'application_url', 'apply_via_us_link'];

    simpleFields.forEach(name => {
        const value = data[name];
        if (value === null || value === undefined || value === '') return;
        const el = document.querySelector(`[name="${name}"]`);
        if (el) el.value = value;
    });

    if (data.deadline) {
        const deadlineEl = document.querySelector('[name="deadline"]');
        if (deadlineEl) deadlineEl.value = data.deadline;
    }

    // لا نلمس فلتر التصنيف لو الذكاء الاصطناعي ما لقاش أي مرحلة صريحة بالنص،
    // حتى يضل "بكالوريوس" الافتراضي محدد وما يوصلش الفورم بصفر تصنيفات (مطلوب حقل إلزامي).
    setCheckboxGroup('categories[]', data.categories || [], true);
    setCheckboxGroup('coverage[]', data.coverage || []);
    setCheckboxGroup('tags[]', data.tags || []);

    ['description', 'overview', 'conditions', 'documents', 'features', 'application_process'].forEach(field => {
        if (data[field]) setQuillHtml(field, data[field]);
    });
}

function setCheckboxGroup(name, values, keepDefaultIfEmpty = false) {
    if (keepDefaultIfEmpty && (!values || values.length === 0)) return;
    document.querySelectorAll(`input[name="${name}"]`).forEach(cb => {
        cb.checked = values.includes(cb.value);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initAllEditors();
});

// تصغير/قص صورة الغلاف تلقائياً عند رفعها لتصبح بنفس نسبة العرض المستخدمة
// بكل الموقع (٤:١)، بدل ما يوصل السيرفر ملف كبير عشوائي الأبعاد ويعتمد على
// القص بالـ CSS فقط وقت العرض.
function setupCoverImageResize(inputId, targetW, targetH) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('change', function () {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                const ctx = canvas.getContext('2d');
                const scale = Math.max(targetW / img.width, targetH / img.height);
                const scaledW = img.width * scale;
                const scaledH = img.height * scale;
                ctx.drawImage(img, (targetW - scaledW) / 2, (targetH - scaledH) / 2, scaledW, scaledH);
                canvas.toBlob(function (blob) {
                    if (!blob) return;
                    const resized = new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(resized);
                    input.files = dt.files;
                }, 'image/jpeg', 0.85);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}
setupCoverImageResize('main_image_input', 1600, 320);
setupCoverImageResize('main_image_mobile_input', 800, 400);
</script>
@endsection