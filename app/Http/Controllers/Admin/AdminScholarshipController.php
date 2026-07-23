<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminScholarshipController extends Controller
{
    // دالة خاصة لتجهيز البيانات المشتركة للصفحة مع تحديث فوري للمنح المنتهية
    private function getCommonData()
    {
        // خطوة ذكية ولحظية: تحويل أي منحة نشطة وتجاوزت الـ deadline إلى closed فوراً عند تحميل الصفحة
        Scholarship::where('status', 'active')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now()->startOfDay())
            ->update(['status' => 'closed']);

        // الآن نقوم بجلب البيانات والإحصائيات وهي محدثة 100% وبشكل صحيح
        return [
            'scholarships' => Scholarship::latest()->paginate(10),
            'stats' => [
                'total' => Scholarship::count(),
                'active' => Scholarship::where('status', 'active')->count(),
                'closed' => Scholarship::where('status', 'closed')->count(),
            ]
        ];
    }

    public function index()
    {
        return view('admin.scholarships', $this->getCommonData());
    }

    public function create()
    {
        return view('admin.scholarships', $this->getCommonData());
    }

    public function store(Request $request)
{
    $request->validate([
        'title_ar' => 'required|string|max:255',
        'title_en' => 'required|string|max:255',
        'country' => 'required|string',
        'university' => 'required|string',
        'deadline' => 'required|date',
        'description' => 'nullable|string',
        'overview' => 'nullable|string',
        'conditions' => 'nullable|string',
        'documents' => 'nullable|string',
        'features' => 'nullable|string',
        'application_process' => 'nullable|string',
        'financial_value' => 'nullable|string|max:255',
        'min_gpa' => 'nullable|numeric|min:0|max:100',
        'applicants_count' => 'nullable|integer|min:0',
        'recommended_tags' => 'nullable|string|max:255', // نستقبله كنص أولاً
        'categories' => 'required|array|min:1',
        'categories.*' => 'in:Bachelor,Master,PhD,Short Course',
        'coverage' => 'nullable|array',
        'tags' => 'nullable|array',
        'application_url' => 'nullable|url|max:500',
        'apply_via_us_link' => 'nullable|url|max:500',
        'main_image' => 'nullable|image|max:2048',
        'main_image_mobile' => 'nullable|image|max:2048',
        'logo_image' => 'nullable|image|max:1048',
        'main_image_url' => 'nullable|url|max:500',
        'main_image_mobile_url' => 'nullable|url|max:500',
        'logo_image_url' => 'nullable|url|max:500',
    ]);

    $data = $request->all();

    // منحة ممكن تشمل أكثر من مرحلة دراسية - category المفرد بيبقى أول
    // عنصر من القائمة فقط لتوافق أي كود قديم لسا بيقرأه مباشرة.
    $data['categories'] = $request->input('categories');
    $data['category'] = $data['categories'][0];

    // 1. حل مشكلة الـ recommended_tags: تحويل النص المفصول بفاصلة إلى مصفوفة (لأن الموديل يعامله كـ array)
    if ($request->filled('recommended_tags')) {
        // تحويل السلسلة النصية "tag1, tag2" إلى مصفوفة ['tag1', 'tag2'] وتنظيف الفراغات.
        // نتخلص أيضاً من أي بايتات UTF-8 غير صالحة قد ينسخها المتصفح عند اللصق (تسبب
        // JsonEncodingException قاتلة وقت الحفظ لأن الموديل يخزّن الحقل كـ JSON array).
        $data['recommended_tags'] = array_map(
            fn ($tag) => trim(mb_convert_encoding($tag, 'UTF-8', 'UTF-8')),
            explode(',', $request->input('recommended_tags'))
        );
    } else {
        $data['recommended_tags'] = [];
    }

    // ملاحظة: عمود price مخصص لسعر خدمة "التقديم عن طريقنا" (راجع تعليق العمود
    // بالمايجريشن: "Price for Apply via Us service; NULL means free grant") - لا
    // علاقة له بنص financial_value الوصفي (زي "راتب شهري 1800 يورو، عقد 3 سنوات").
    // محاولة استخراج رقم من هذا النص عبر تجريد كل شي غير رقمي كانت تلصق كل الأرقام
    // المتفرقة ببعض (سنوات + مبلغ + نسبة%) فتنتج رقماً ضخماً يتجاوز حجم العمود
    // ويُسقط الحفظ بالكامل (SQL Out of range). العمود يبقى فارغاً (يعني منحة
    // مجانية) ما لم تتوفر لاحقاً خانة إدخال مخصصة فعلاً لسعر الخدمة.

    // ضمان عدم تصفير الفلاتر الأساسية
    $data['tags'] = $request->has('tags') ? $request->input('tags') : [];
    $data['coverage'] = $request->has('coverage') ? $request->input('coverage') : [];

    // رفع الصور: نخزّن المسار النسبي فقط (وليس رابطاً كاملاً جاهزاً)، لأن
    // الرابط الكامل المُخزَّن مسبقاً يتعطّل لو تغيّر النطاق أو البروتوكول
    // لاحقاً. الرابط الفعلي يُبنى تلقائياً عند العرض دائماً (Scholarship model).
    if ($request->hasFile('main_image')) {
        $file = $request->file('main_image');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['main_image'] = $file->storeAs('scholarships/main-images', $filename, 'public');
    } elseif ($request->filled('main_image_url')) {
        $data['main_image'] = $request->input('main_image_url');
    }

    if ($request->hasFile('main_image_mobile')) {
        $file = $request->file('main_image_mobile');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['main_image_mobile'] = $file->storeAs('scholarships/main-images-mobile', $filename, 'public');
    } elseif ($request->filled('main_image_mobile_url')) {
        $data['main_image_mobile'] = $request->input('main_image_mobile_url');
    }

    if ($request->hasFile('logo_image')) {
        $file = $request->file('logo_image');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['logo_image'] = $file->storeAs('scholarships/logos', $filename, 'public');
    } elseif ($request->filled('logo_image_url')) {
        $data['logo_image'] = $request->input('logo_image_url');
    }

    // أي انهيار غير متوقع هون (مثلاً بيانات لا تُحفظ كـ JSON، أو مشكلة تخزين) كان
    // يظهر كصفحة "500 خطأ في الخادم" فارغة تماماً بدون أي تفاصيل للأدمن. بدل هيك،
    // نعرض رسالة واضحة بنفس صندوق الأخطاء المستخدم أصلاً بهاي الصفحة، ونسجّل
    // التفاصيل الحقيقية بالـ log حتى يمكن تتبعها.
    try {
        Scholarship::create(array_merge($data, ['status' => 'active']));
    } catch (\Throwable $e) {
        Log::error('Failed to create scholarship: ' . $e->getMessage());
        return redirect()->back()->withInput()->withErrors([
            'error' => 'تعذّر حفظ المنحة. يرجى مراجعة الحقول (خصوصاً النصوص المنسوخة من مصادر خارجية) والمحاولة مجدداً.',
        ]);
    }

    // إشعار الطلاب بيصير عبر جدولة مستقلة (routes/console.php، كل دقيقة) وليس هون
    // مباشرة - لأنه على الاستضافة الحية ما في queue worker، وإرسال إيميلات لكل
    // الطلاب داخل نفس الطلب كان يعلّق صفحة الأدمن لحد ما تخلص كل الإيميلات.

    return redirect()->route('admin.scholarships.index')->with('success', 'تم نشر المنحة بنجاح!');
}
public function edit(Scholarship $scholarship)
    {
        return view('admin.scholarships.edit', compact('scholarship'));
    }

public function update(Request $request, Scholarship $scholarship)
{
    $request->validate([
        'title_ar' => 'required|string|max:255',
        'title_en' => 'required|string|max:255',
        'country' => 'required|string',
        'university' => 'required|string',
        'deadline' => 'required|date',
        'description' => 'nullable|string',
        'overview' => 'nullable|string',
        'conditions' => 'nullable|string',
        'documents' => 'nullable|string',
        'features' => 'nullable|string',
        'application_process' => 'nullable|string',
        'financial_value' => 'nullable|string|max:255',
        'min_gpa' => 'nullable|numeric|min:0|max:100',
        'applicants_count' => 'nullable|integer|min:0',
        'recommended_tags' => 'nullable|string|max:255',
        'categories' => 'required|array|min:1',
        'categories.*' => 'in:Bachelor,Master,PhD,Short Course',
        'coverage' => 'nullable|array',
        'tags' => 'nullable|array',
        'application_url' => 'nullable|url|max:500',
        'apply_via_us_link' => 'nullable|url|max:500',
        'status' => 'required|in:active,closed',
        'main_image' => 'nullable|image|max:2048',
        'main_image_mobile' => 'nullable|image|max:2048',
        'logo_image' => 'nullable|image|max:1048',
        'main_image_url' => 'nullable|url|max:500',
        'main_image_mobile_url' => 'nullable|url|max:500',
        'logo_image_url' => 'nullable|url|max:500',
    ]);

    $data = $request->all();

    // منحة ممكن تشمل أكثر من مرحلة دراسية - category المفرد بيبقى أول
    // عنصر من القائمة فقط لتوافق أي كود قديم لسا بيقرأه مباشرة.
    $data['categories'] = $request->input('categories');
    $data['category'] = $data['categories'][0];

    // 1. معالجة الـ recommended_tags لمنع تعارض مصفوفة الـ Model وحذف الفلاتر.
    // نفس معالجة الترميز المطبّقة في store() لمنع JsonEncodingException.
    if ($request->filled('recommended_tags')) {
        $data['recommended_tags'] = array_map(
            fn ($tag) => trim(mb_convert_encoding($tag, 'UTF-8', 'UTF-8')),
            explode(',', $request->input('recommended_tags'))
        );
    } else {
        $data['recommended_tags'] = [];
    }

    // ملاحظة: عمود price مخصص لسعر خدمة "التقديم عن طريقنا" ولا علاقة له بنص
    // financial_value الوصفي - راجع نفس الشرح المفصّل في store() أعلاه. لا نلمسه
    // هون فيبقى محتفظاً بقيمته الحالية بدل ما نستنتج رقماً خاطئاً من النص.

    // حماية الفلاتر عند التعديل: إذا لم يتم اختيار أي checkbox، نحتفظ بالقديم ولا نحذفه بالكامل
    $data['tags'] = $request->has('tags') ? $request->input('tags') : $scholarship->tags;
    $data['coverage'] = $request->has('coverage') ? $request->input('coverage') : $scholarship->coverage;

    // تحديث الصور
    if ($request->hasFile('main_image')) {
        $file = $request->file('main_image');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['main_image'] = $file->storeAs('scholarships/main-images', $filename, 'public');
    } elseif ($request->filled('main_image_url')) {
        $data['main_image'] = $request->input('main_image_url');
    } else {
        // نستخدم القيمة الخام (غير المحوّلة عبر accessor) حتى لا يُعاد حفظ
        // رابط كامل جاهز بدل المسار النسبي الأصلي عند عدم تغيير الصورة.
        $data['main_image'] = $scholarship->getRawOriginal('main_image');
    }

    if ($request->hasFile('main_image_mobile')) {
        $file = $request->file('main_image_mobile');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['main_image_mobile'] = $file->storeAs('scholarships/main-images-mobile', $filename, 'public');
    } elseif ($request->filled('main_image_mobile_url')) {
        $data['main_image_mobile'] = $request->input('main_image_mobile_url');
    } else {
        $data['main_image_mobile'] = $scholarship->getRawOriginal('main_image_mobile');
    }

    if ($request->hasFile('logo_image')) {
        $file = $request->file('logo_image');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['logo_image'] = $file->storeAs('scholarships/logos', $filename, 'public');
    } elseif ($request->filled('logo_image_url')) {
        $data['logo_image'] = $request->input('logo_image_url');
    } else {
        $data['logo_image'] = $scholarship->getRawOriginal('logo_image');
    }

    try {
        $scholarship->update($data);
    } catch (\Throwable $e) {
        Log::error('Failed to update scholarship #' . $scholarship->id . ': ' . $e->getMessage());
        return redirect()->back()->withInput()->withErrors([
            'error' => 'تعذّر حفظ التعديلات. يرجى مراجعة الحقول (خصوصاً النصوص المنسوخة من مصادر خارجية) والمحاولة مجدداً.',
        ]);
    }

    return redirect()->route('admin.scholarships.index')->with('success', 'تم تحديث المنحة بنجاح!');
}
    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return redirect()->route('admin.scholarships.index')->with('success', 'تم حذف المنحة بنجاح!');
    }

    public function generateAllSections(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'university' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'category' => 'required|string|max:50',
        ]);

        try {
            $service = new \App\Services\GroqChatServiceScholarshipPrompt();
            
            $sections = $service->generateAllSections(
                $request->title_ar,
                $request->university,
                $request->country,
                $request->category
            );

            if (isset($sections['error'])) {
                return response()->json(['success' => false, 'error' => $sections['error']], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'description' => $sections['description'] ?? '',
                    'overview'   => $sections['overview'] ?? '',
                    'conditions' => $sections['conditions'] ?? '',
                    'documents'  => $sections['documents'] ?? '',
                    'features'   => $sections['features'] ?? '',
                    'application_process' => $sections['application_process'] ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Groq generateAllSections error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'خطأ داخلي: ' . $e->getMessage()], 500);
        }
    }

    public function aiAutofillFromText(Request $request)
    {
        $request->validate([
            'raw_text' => 'required|string|max:20000',
        ]);

        try {
            $service = new \App\Services\GroqChatServiceScholarshipPrompt();

            $data = $service->extractFromRawText($request->raw_text);

            if (isset($data['error'])) {
                return response()->json(['success' => false, 'error' => $data['error']], 400);
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Groq aiAutofillFromText error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'خطأ داخلي: ' . $e->getMessage()], 500);
        }
    }
}