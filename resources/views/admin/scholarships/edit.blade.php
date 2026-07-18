@extends('layouts.admin')

@section('title', 'تعديل المنحة')
@section('breadcrumb', 'المنح الدراسية > تعديل')

@section('content')

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<div class="max-w-7xl mx-auto space-y-6">
    @if (session('success'))
        <div class="bg-emerald-500 text-white p-4 rounded-2xl font-black text-sm shadow-lg shadow-emerald-100 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
        <form action="{{ route('admin.scholarships.update', $scholarship) }}" method="POST" id="scholarshipForm" enctype="multipart/form-data">
            @csrf 
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                {{-- الحقول الأساسية --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-6">
                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">عنوان المنحة (عربي)</label>
                            <input type="text" name="title_ar" value="{{ old('title_ar', $scholarship->title_ar) }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Scholarship Title (English)</label>
                            <input type="text" name="title_en" value="{{ old('title_en', $scholarship->title_en) }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all text-left">
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">القيمة المالية</label>
                            <input type="text" name="financial_value" value="{{ old('financial_value', $scholarship->financial_value) }}" placeholder="₪50,000 كامل التمويل" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الحد الأدنى للمعدل (اختياري)</label>
                            <input type="number" name="min_gpa" value="{{ old('min_gpa', $scholarship->min_gpa) }}" step="0.1" min="0" max="100" placeholder="مثال: 80" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            <p class="text-[10px] text-slate-400 mt-1">بيُستخدم لحساب نسبة توافق الطالب بدقة</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">عدد المتقدمين</label>
                                <input type="number" name="applicants_count" value="{{ old('applicants_count', $scholarship->applicants_count) }}" min="0" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">
                            </div>
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الكلمات المفتاحية</label>
                                <input type="text" name="recommended_tags" value="{{ old('recommended_tags', is_array($scholarship->recommended_tags) ? implode(', ', $scholarship->recommended_tags) : $scholarship->recommended_tags) }}" placeholder="ممولة كاملاً, ماجستير" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-gold-300 outline-none transition-all">                        </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الدولة</label>
                                <input type="text" name="country" value="{{ old('country', $scholarship->country) }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold outline-none">
                            </div>
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الجامعة</label>
                                <input type="text" name="university" value="{{ old('university', $scholarship->university) }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">الموعد النهائي</label>
                                <input type="date" name="deadline" value="{{ old('deadline', $scholarship->deadline ? \Carbon\Carbon::parse($scholarship->deadline)->format('Y-m-d') : '') }}" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold outline-none">
                            </div>
                            <div>
                                <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">التصنيف (يمكن اختيار أكثر من مرحلة)</label>
                                @php $selectedCategories = old('categories', $scholarship->categories_list); @endphp
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach(['Bachelor' => 'بكالوريوس', 'Master' => 'ماجستير', 'PhD' => 'دكتوراه', 'Short Course' => 'كورس قصير'] as $key => $label)
                                    <label class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs font-bold cursor-pointer hover:border-gold-300 transition-all has-[:checked]:border-gold-400 has-[:checked]:bg-gold-50">
                                        <input type="checkbox" name="categories[]" value="{{ $key }}" class="w-4 h-4 rounded text-gold-600 focus:ring-gold-500" {{ in_array($key, $selectedCategories) ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">رابط التقديم المجاني</label>
                            <input type="url" name="application_url" value="{{ old('application_url', $scholarship->application_url) }}" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold outline-none text-left">
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase mb-2 block tracking-widest">رابط "التقديم عن طريقنا" (واتساب / تيليجرام)</label>
                            <input type="url" name="apply_via_us_link" value="{{ old('apply_via_us_link', $scholarship->apply_via_us_link) }}" placeholder="https://wa.me/9705xxxxxxx" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-3 text-sm font-bold outline-none text-left">
                        </div>
                    </div>

                    {{-- قسم رفع ملفات وصور المنحة --}}
                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-black text-slate-700">🖼️ تعديل الوسائط والملفات المرفقة</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[11px] font-black text-slate-400 block mb-2">صورة المنحة (Cover)</label>
                                <input type="file" name="main_image" accept="image/*" class="text-xs">
                                <input type="url" name="main_image_url" placeholder="أو الصق رابط صورة مباشر (https://...)" dir="ltr"
                                       class="w-full bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:border-gold-300 outline-none transition mt-2">
                                @if($scholarship->main_image)
                                    <img src="{{ $scholarship->main_image }}" class="w-20 h-20 object-cover mt-2 rounded-xl border">
                                @endif
                            </div>
                            <div>
                                <label class="text-[11px] font-black text-slate-400 block mb-2">شعار المنحة</label>
                                <input type="file" name="logo_image" accept="image/*" class="text-xs">
                                <input type="url" name="logo_image_url" placeholder="أو الصق رابط صورة مباشر (https://...)" dir="ltr"
                                       class="w-full bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:border-gold-300 outline-none transition mt-2">
                                @if($scholarship->logo_image)
                                    <img src="{{ $scholarship->logo_image }}" class="w-16 h-16 object-contain mt-2 rounded-xl border">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- المحررات المتقدمة --}}
                    <div class="space-y-6 mt-6">
                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">📝 الوصف العام للمنحة</label>
                            <input type="hidden" name="description" id="description_input" value="{{ old('description', $scholarship->description) }}">
                            <div class="editor-container"><div id="description_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">📖 نظرة عامة</label>
                            <input type="hidden" name="overview" id="overview_input" value="{{ old('overview', $scholarship->overview) }}">
                            <div class="editor-container"><div id="overview_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">✅ الشروط</label>
                            <input type="hidden" name="conditions" id="conditions_input" value="{{ old('conditions', $scholarship->conditions) }}">
                            <div class="editor-container"><div id="conditions_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">📄 المستندات المطلوبة</label>
                            <input type="hidden" name="documents" id="documents_input" value="{{ old('documents', $scholarship->documents) }}">
                            <div class="editor-container"><div id="documents_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">⭐ المميزات</label>
                            <input type="hidden" name="features" id="features_input" value="{{ old('features', $scholarship->features) }}">
                            <div class="editor-container"><div id="features_editor" class="quill-editor"></div></div>
                        </div>

                        <div class="word-editor-card space-y-2">
                            <label class="text-xs font-black text-slate-700 flex items-center gap-2">🧭 آلية التقديم</label>
                            <input type="hidden" name="application_process" id="application_process_input" value="{{ old('application_process', $scholarship->application_process) }}">
                            <div class="editor-container"><div id="application_process_editor" class="quill-editor"></div></div>
                        </div>
                    </div>
                </div>

                {{-- الـ Sidebar الجانبي في صفحة التعديل --}}
                <div class="lg:col-span-4 space-y-8 lg:border-r lg:border-slate-100 lg:pr-6">
                    {{-- حقول حالة النشاط --}}
                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-4">
                        <label class="text-[11px] font-black text-slate-400 uppercase block tracking-widest text-gold-600">حالة المنحة الحالية</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="active" {{ old('status', $scholarship->status) == 'active' ? 'checked' : '' }} class="w-5 h-5 rounded-full text-gold-600">
                                <span class="font-black text-sm text-emerald-600">نشطة</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="closed" {{ old('status', $scholarship->status) == 'closed' ? 'checked' : '' }} class="w-5 h-5 rounded-full text-gold-600">
                                <span class="font-black text-sm text-rose-600">منتهية</span>
                            </label>
                        </div>
                    </div>

                    {{-- الفلاتر (التخصصات - Tags) المفقودة --}}
                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-4">
                        <label class="text-[11px] font-black text-slate-400 uppercase block tracking-widest text-gold-600">التخصصات (Tags)</label>
                        <div class="flex flex-wrap gap-2">
                            @php 
                                $currentTags = is_array($scholarship->tags) ? $scholarship->tags : json_decode($scholarship->tags, true) ?? [];
                            @endphp
                            @foreach(['هندسة', 'طب', 'تقنية', 'إدارة', 'علوم', 'فن'] as $tag)
                            <label class="group cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag }}" {{ in_array($tag, $currentTags) ? 'checked' : '' }} class="hidden peer">
                                <span class="px-4 py-2 bg-slate-50 text-slate-500 rounded-xl text-[10px] font-black peer-checked:bg-gold-600 peer-checked:text-white transition-all border border-slate-100 inline-block">
                                    {{ $tag }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- الفلاتر (التغطية الماليّة - Coverage) المفقودة --}}
                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-6 shadow-sm space-y-4">
                        <label class="text-[11px] font-black text-slate-400 uppercase block tracking-widest text-gold-600">التغطية المالية (Coverage)</label>
                        <div class="space-y-3">
                            @php 
                                $currentCoverage = is_array($scholarship->coverage) ? $scholarship->coverage : json_decode($scholarship->coverage, true) ?? [];
                            @endphp
                            @foreach(['تمويل كامل', 'إعفاء من الرسوم', 'راتب شهري', 'تأمين صحي', 'سكن'] as $cov)
                            <label class="flex items-center gap-3 group cursor-pointer">
                                <input type="checkbox" name="coverage[]" value="{{ $cov }}" {{ in_array($cov, $currentCoverage) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-200 text-gold-600">
                                <span class="text-xs font-bold text-slate-600">{{ $cov }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="p-5 bg-slate-900 rounded-[2rem] shadow-xl">
                            <button type="submit" class="w-full bg-gold-500 text-white py-4 rounded-2xl font-black text-sm hover:bg-gold-400 transition-all">
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.scholarships.index') }}" class="w-full block mt-3 text-center text-slate-400 font-bold text-[10px] hover:text-white transition-colors">
                                إلغاء وتراجع
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<style>
  /* [تحتفظ بنفس التنسيقات والـ CSS الخاصة بـ Quill والموجودة في ملفك لضمان تجربة مستخدم أنيقة] */
  .word-editor-card { display: flex; flex-direction: column; }
  .word-editor-card .editor-container { position: relative; width: 100%; }
  .word-editor-card .ql-toolbar.ql-snow { border: 1px solid rgba(15, 23, 42, 0.08) !important; background: #f8fafc !important; border-top-left-radius: 0.75rem !important; border-top-right-radius: 0.75rem !important; text-align: right; direction: rtl; padding: 8px !important; }
  .word-editor-card .ql-container.ql-snow { border: 1px solid rgba(15, 23, 42, 0.08) !important; border-top: none !important; border-bottom-left-radius: 0.75rem !important; border-bottom-right-radius: 0.75rem !important; min-height: 180px; max-height: 400px; overflow-y: auto; background: #ffffff; }
  .word-editor-card .ql-editor { direction: rtl; text-align: right; font-size: 0.9rem; font-weight: 600; line-height: 1.7; color: #334155; padding: 14px !important; }
  .word-editor-card .ql-editor img { max-width: 100%; height: auto; border-radius: 0.5rem; }
</style>

<script>
const editors = {};
function initAllEditors() {
    // أضفنا الـ description هنا ليعمل المحرر الخامس بكفاءة في صفحة التعديل
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
                placeholder: `أدخل تفاصيل ${field}...`,
                modules: { toolbar: { container: toolbarOptions, handlers: { image: () => uploadEditorImage(field) } } }
            });

            const initialValue = document.querySelector(inputSelector).value;
            if (initialValue) {
                editors[field].root.innerHTML = initialValue;
            }

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
document.addEventListener("DOMContentLoaded", () => { initAllEditors(); });
</script>
@endsection