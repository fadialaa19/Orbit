<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * نقطة نشر منح دراسية مباشرة عبر API، محمية بمفتاح ثابت (CheckScholarshipAdminApiKey)
 * بدل تسجيل دخول أدمن عادي - مخصصة لتكامل خارجي موثوق يرسل بيانات منحة جاهزة بعد
 * موافقة الأدمن الصريحة على كل عملية نشر. نفس منطق الحفظ المستخدم في
 * AdminScholarshipController::store لكن مُدخلاته JSON بدل نموذج HTML.
 */
class AdminScholarshipIngestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'recommended_tags' => 'nullable|array',
            'recommended_tags.*' => 'string|max:100',
            'categories' => 'required|array|min:1',
            'categories.*' => 'in:Bachelor,Master,PhD,Short Course',
            'coverage' => 'nullable|array',
            'coverage.*' => 'string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'application_url' => 'nullable|url|max:500',
            'apply_via_us_link' => 'nullable|url|max:500',
            'main_image_url' => 'nullable|url|max:500',
            'logo_image_url' => 'nullable|url|max:500',
        ]);

        $data = $validated;
        $data['category'] = $data['categories'][0];

        // نفس معالجة الترميز المطبّقة بالنموذج العادي، احتياطاً لو وصل أي بايت
        // UTF-8 غير صالح ضمن النص (راجع نفس الإصلاح في AdminScholarshipController).
        $data['recommended_tags'] = collect($data['recommended_tags'] ?? [])
            ->map(fn ($tag) => trim(mb_convert_encoding($tag, 'UTF-8', 'UTF-8')))
            ->values()
            ->all();

        $data['coverage'] = $data['coverage'] ?? [];
        $data['tags'] = $data['tags'] ?? [];

        if (!empty($data['financial_value'])) {
            $numericPrice = (float) preg_replace('/[^0-9.]/', '', $data['financial_value']);
            $data['price'] = $numericPrice > 0 ? $numericPrice : 0.00;
        }

        if (!empty($validated['main_image_url'])) {
            $data['main_image'] = $validated['main_image_url'];
        }
        if (!empty($validated['logo_image_url'])) {
            $data['logo_image'] = $validated['logo_image_url'];
        }
        unset($data['main_image_url'], $data['logo_image_url']);

        try {
            $scholarship = Scholarship::create(array_merge($data, ['status' => 'active']));
        } catch (\Throwable $e) {
            Log::error('API scholarship ingest failed: ' . $e->getMessage());
            return response()->json(['error' => 'تعذّر حفظ المنحة: ' . $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'id' => $scholarship->id,
            'url' => route('guest.scholarships.show', $scholarship->id),
        ], 201);
    }
}
