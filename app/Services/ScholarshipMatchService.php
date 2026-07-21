<?php

namespace App\Services;

use App\Models\ScholarshipMatchScore;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScholarshipMatchService
{
    private AiCompletionService $ai;

    // نفس قائمة المستندات المستخدمة في صفحة الملف الشخصي (resources/views/dashboard/profile.blade.php)
    private const DOCUMENT_LABELS = [
        'passport' => 'جواز السفر',
        'national_id' => 'الهوية الشخصية',
        'high_school_cert' => 'شهادة الثانوية',
        'birth_cert' => 'شهادة الميلاد',
        'cv' => 'السيرة الذاتية (CV)',
        'language_cert' => 'شهادة لغة',
        'courses_cert' => 'شهادة دورات',
        'recommendation' => 'خطاب توصية',
        'intent_letter' => 'خطاب نية',
    ];

private const SYSTEM_PROMPT = <<<'PROMPT'
أنتِ مستشارة قبولات جامعية خبيرة متخصصة في المنح الدراسية الدولية. مهمتك تحليل مدى توافق ملف طالب حقيقي مع كل منحة من قائمة، وإعطاء نسبة قبول واقعية لكل واحدة - مش نسب متفائلة أو مجاملة.

كل منحة بييجي معها الحقول التالية جاهزة مسبقًا (محسوبة رياضيًا، مش تخمين):
- min_gpa: الحد الأدنى الرسمي للمعدل (لو موجود). لو null فمفيش حد أدنى رسمي محدد.
- student_relevant_gpa: معدل الطالب في المرحلة المناسبة لهذه المنحة تحديدًا (مثلاً معدل الثانوية لمنح البكالوريوس، معدل البكالوريوس لمنح الماجستير...).
- meets_gpa_requirement: true لو الطالب مستوفي الحد الأدنى، false لو لأ، null لو مفيش حد أدنى محدد أو معدل الطالب غير مسجل.

هذه القيم حقائق مؤكدة - لا تتجاهليها ولا تعيدي تخمين المعدل من نص الشروط. لو meets_gpa_requirement = false، هذا سبب رئيسي لنسبة منخفضة ويجب ذكره صراحة في gaps بالرقمين (معدل الطالب مقابل الحد الأدنى). لو true، اعتبريه نقطة قوة واضحة في matched_criteria.

كوني صارمة وواقعية بباقي المعايير:
- لو ناقصة مستندات أساسية (زي شهادة لغة لمنحة تتطلبها)، وضحي هذا في gaps ونزّلي النسبة.
- لو الملف قوي ومطابق فعلاً (معدل مستوفى + مستندات موجودة)، مسموح تدّي نسبة عالية (85+).
- لا تكرري نفس النص لكل منحة - كل تحليل يجب يعكس تفاصيل تلك المنحة تحديدًا.

أعيدي ردك ككائن JSON صالح فقط بهذا الشكل بالضبط:
{
  "results": [
    {
      "scholarship_id": رقم المنحة كما ورد في القائمة,
      "score": رقم صحيح من 0 إلى 100,
      "summary": "جملة أو جملتين توضح سبب النسبة",
      "matched_criteria": ["نقطة تطابق محددة 1", "نقطة تطابق محددة 2"],
      "gaps": ["نقطة نقص محددة 1", "نقطة نقص محددة 2"]
    }
  ]
}
PROMPT;

    public function __construct(?AiCompletionService $ai = null)
    {
        $this->ai = $ai ?? new AiCompletionService();
    }

    /**
     * Return cached+fresh scores where available, and compute the rest in one batched
     * Groq call. Returns a [scholarship_id => ScholarshipMatchScore] map.
     */
    public function getOrComputeScores(User $user, Collection $scholarships): array
    {
        $existing = ScholarshipMatchScore::where('user_id', $user->id)
            ->whereIn('scholarship_id', $scholarships->pluck('id'))
            ->get()
            ->keyBy('scholarship_id');

        $needsComputing = $scholarships->filter(function ($scholarship) use ($existing, $user) {
            $cached = $existing->get($scholarship->id);
            return !$cached || !$cached->isFresh($user, $scholarship);
        });

        // ->all() keeps ScholarshipMatchScore model objects as-is (unlike ->toArray(),
        // which would recursively convert them to plain arrays and break ->score access
        // in views for the "already cached" branch while freshly computed ones stay objects).
        $results = $existing->all();

        if ($needsComputing->isNotEmpty()) {
            $computed = $this->computeBatch($user, $needsComputing);
            foreach ($computed as $scholarshipId => $score) {
                $results[$scholarshipId] = $score;
            }
        }

        return $results;
    }

    /**
     * Compute (and persist) scores for the given scholarships against a student's
     * real profile, in a single Groq request. Returns [scholarship_id => ScholarshipMatchScore].
     */
    public function computeBatch(User $user, Collection $scholarships): array
    {
        if ($scholarships->isEmpty()) {
            return [];
        }

        $profileSummary = $this->buildProfileSummary($user);
        $scholarshipList = $scholarships->map(function ($s) use ($user) {
            $studentGpa = $this->relevantStudentGpa($user, $s->categories_list);
            $minGpa = $s->min_gpa !== null ? (float) $s->min_gpa : null;
            $meetsGpa = ($minGpa !== null && $studentGpa !== null) ? ($studentGpa >= $minGpa) : null;

            return [
                'scholarship_id' => $s->id,
                'title' => $s->title_ar,
                'category' => $s->category_label,
                'country' => $s->country,
                'min_gpa' => $minGpa,
                'student_relevant_gpa' => $studentGpa,
                'meets_gpa_requirement' => $meetsGpa,
                'conditions' => Str::limit(strip_tags((string) $s->conditions), 600),
            ];
        })->values()->toArray();

        $userPrompt = "ملف الطالب:\n" . $profileSummary
            . "\n\nالمنح المطلوب تحليلها (بصيغة JSON):\n" . json_encode($scholarshipList, JSON_UNESCAPED_UNICODE);

        $aiResult = $this->ai->complete([
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
            ['role' => 'user', 'content' => $userPrompt],
        ], ['temperature' => 0.3, 'max_tokens' => 4096, 'json_mode' => true, 'timeout' => 45]);

        if (!$aiResult['success']) {
            Log::error('ScholarshipMatchService AI error: ' . $aiResult['error']);
            return [];
        }

        $raw = $aiResult['content'];
        $parsed = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($parsed['results'])) {
            Log::error('ScholarshipMatchService JSON parse error. Raw: ' . $raw);
            return [];
        }

        $saved = [];
        foreach ($parsed['results'] as $result) {
            if (!isset($result['scholarship_id'], $result['score'])) {
                continue;
            }

            $score = ScholarshipMatchScore::updateOrCreate(
                ['user_id' => $user->id, 'scholarship_id' => $result['scholarship_id']],
                [
                    'score' => max(0, min(100, (int) $result['score'])),
                    'summary' => $result['summary'] ?? null,
                    'matched_criteria' => $result['matched_criteria'] ?? [],
                    'gaps' => $result['gaps'] ?? [],
                    'computed_at' => now(),
                ]
            );

            $saved[$result['scholarship_id']] = $score;
        }

        return $saved;
    }

    /**
     * الحقل المناسب من معدلات الطالب حسب مرحلة المنحة (بكالوريوس ← معدل الثانوية،
     * ماجستير ← معدل البكالوريوس، دكتوراه ← معدل الماجستير)، بدل ما نترك الذكاء
     * الاصطناعي يخمّن أي معدل يقارن بحد المنحة الأدنى. المنحة ممكن تشمل أكثر
     * من مرحلة (مثلاً بكالوريوس وماجستير معاً) - بنستخدم أفضل معدل متاح للطالب
     * بين كل المراحل المطروحة، لأنه يقدر يقدّم عن طريق أي مسار يناسب وضعه.
     */
    private function relevantStudentGpa(User $user, array $categories): ?float
    {
        $gpas = [];

        foreach ($categories as $category) {
            $gpa = match ($category) {
                'Bachelor' => $user->high_school_gpa,
                'Master' => $user->bachelor_gpa,
                'PhD' => $user->master_gpa,
                default => null,
            };
            if ($gpa !== null) {
                $gpas[] = (float) $gpa;
            }
        }

        if (!empty($gpas)) {
            return max($gpas);
        }

        $fallback = $user->bachelor_gpa ?? $user->high_school_gpa;
        return $fallback !== null ? (float) $fallback : null;
    }

    private function buildProfileSummary(User $user): string
    {
        $lines = [];
        $lines[] = '- الدولة: ' . ($user->country ?: 'غير محدد');

        if ($user->high_school_gpa) {
            $lines[] = '- معدل الثانوية العامة: ' . $user->high_school_gpa . '%';
        }
        if ($user->bachelor_gpa) {
            $lines[] = '- معدل البكالوريوس: ' . $user->bachelor_gpa . ' (' . ($user->bachelor_degree ?: 'غير محدد التخصص') . ')';
        }
        if ($user->master_gpa) {
            $lines[] = '- معدل الماجستير: ' . $user->master_gpa . ' (' . ($user->master_degree ?: 'غير محدد التخصص') . ')';
        }

        if (is_array($user->languages) && count($user->languages)) {
            $langs = collect($user->languages)->map(fn ($l) => ($l['name'] ?? '') . (!empty($l['level']) ? ' (' . $l['level'] . ')' : ''))->implode('، ');
            $lines[] = '- اللغات: ' . $langs;
        } else {
            $lines[] = '- اللغات: لم يُضف الطالب أي شهادات لغة بعد';
        }

        $approvedDocs = $user->documents()->where('status', 'approved')->pluck('category')->filter()->toArray();
        $approvedLabels = collect(self::DOCUMENT_LABELS)->only($approvedDocs)->values()->implode('، ');
        $missingDocs = collect(self::DOCUMENT_LABELS)->except($user->documents()->pluck('category')->filter()->toArray());

        $lines[] = '- مستندات موافق عليها: ' . ($approvedLabels ?: 'لا يوجد');
        $lines[] = '- مستندات لم يرفعها الطالب بعد: ' . ($missingDocs->values()->implode('، ') ?: 'لا يوجد');

        return implode("\n", $lines);
    }
}
