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
        'category' => 'required|string',
        'coverage' => 'nullable|array',
        'tags' => 'nullable|array',
        'application_url' => 'nullable|url|max:500',
        'apply_via_us_link' => 'nullable|url|max:500',
        'main_image' => 'nullable|image|max:2048',
        'logo_image' => 'nullable|image|max:1048',
        'main_image_url' => 'nullable|url|max:500',
        'logo_image_url' => 'nullable|url|max:500',
    ]);

    $data = $request->all();

    // 1. حل مشكلة الـ recommended_tags: تحويل النص المفصول بفاصلة إلى مصفوفة (لأن الموديل يعامله كـ array)
    if ($request->filled('recommended_tags')) {
        // تحويل السلسلة النصية "tag1, tag2" إلى مصفوفة ['tag1', 'tag2'] وتنظيف الفراغات
        $data['recommended_tags'] = array_map('trim', explode(',', $request->input('recommended_tags')));
    } else {
        $data['recommended_tags'] = [];
    }

    // 2. حل مشكلة الثبات على 50000: نقوم بإسناد القيمة المدخلة في الـ price أيضاً إذا كانت قاعدة البيانات تعتمد عليه
    if ($request->filled('financial_value')) {
        // استخراج الأرقام فقط من القيمة المالية إذا كنت تريد تخزينها كـ decimal في الـ price
        $numericPrice = (float) preg_replace('/[^0-9.]/', '', $request->input('financial_value'));
        $data['price'] = $numericPrice > 0 ? $numericPrice : 0.00;
    } else {
        $data['price'] = 0.00;
    }

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

    if ($request->hasFile('logo_image')) {
        $file = $request->file('logo_image');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['logo_image'] = $file->storeAs('scholarships/logos', $filename, 'public');
    } elseif ($request->filled('logo_image_url')) {
        $data['logo_image'] = $request->input('logo_image_url');
    }

    $scholarship = Scholarship::create(array_merge($data, ['status' => 'active']));

    $this->notifyStudentsOfNewScholarship($scholarship);

    return redirect()->route('admin.scholarships.index')->with('success', 'تم نشر المنحة بنجاح!');
}

/**
 * إشعار كل الطلاب المسجلين (داخل الموقع + بريد إلكتروني) عند نشر منحة جديدة.
 * كل طالب مُعزول بـ try/catch مستقل حتى لا يوقف فشل إشعار واحد باقي الطلاب.
 */
private function notifyStudentsOfNewScholarship(Scholarship $scholarship): void
{
    \App\Models\User::where('role', 'student')->get()->each(function ($student) use ($scholarship) {
        try {
            $student->notify(new \App\Notifications\NewScholarshipPublished($scholarship));
        } catch (\Exception $e) {
            Log::error("Failed to notify student #{$student->id} of new scholarship #{$scholarship->id}: " . $e->getMessage());
        }
    });
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
        'category' => 'required|string',
        'coverage' => 'nullable|array',
        'tags' => 'nullable|array',
        'application_url' => 'nullable|url|max:500',
        'apply_via_us_link' => 'nullable|url|max:500',
        'status' => 'required|in:active,closed',
        'main_image' => 'nullable|image|max:2048',
        'logo_image' => 'nullable|image|max:1048',
        'main_image_url' => 'nullable|url|max:500',
        'logo_image_url' => 'nullable|url|max:500',
    ]);

    $data = $request->all();

    // 1. معالجة الـ recommended_tags لمنع تعارض مصفوفة الـ Model وحذف الفلاتر
    if ($request->filled('recommended_tags')) {
        $data['recommended_tags'] = array_map('trim', explode(',', $request->input('recommended_tags')));
    } else {
        $data['recommended_tags'] = [];
    }

    // 2. تحديث الـ price بناءً على الـ financial_value المتغيرة لمنع ثبات الـ 50000
    if ($request->filled('financial_value')) {
        $numericPrice = (float) preg_replace('/[^0-9.]/', '', $request->input('financial_value'));
        $data['price'] = $numericPrice > 0 ? $numericPrice : 0.00;
    }

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

    if ($request->hasFile('logo_image')) {
        $file = $request->file('logo_image');
        $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $data['logo_image'] = $file->storeAs('scholarships/logos', $filename, 'public');
    } elseif ($request->filled('logo_image_url')) {
        $data['logo_image'] = $request->input('logo_image_url');
    } else {
        $data['logo_image'] = $scholarship->getRawOriginal('logo_image');
    }

    $scholarship->update($data);

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
}