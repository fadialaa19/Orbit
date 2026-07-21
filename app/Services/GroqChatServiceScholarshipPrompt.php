<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatServiceScholarshipPrompt
{
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model = 'llama-3.3-70b-versatile';

    private const SYSTEM_PROMPT = <<<'PROMPT'
أنت كاتب محتوى متخصص في المنح الدراسية. مهمتك كتابة وصف احترافي وجذاب لمنحة دراسية واحدة فقط.

قواعد الوصف:
1. ابدأ بعنوان المنحة البارز.
2. قدم نظرة عامة مثيرة عن الفرصة.
3. قائمة بالمميزات الرئيسية (الدراسة، المالية، السكن، التأمين).
4. الشروط والمتطلبات بوضوح.
5. خطوات التقديم المبسطة.
6. تاريخ الإغلاق المهم.
7. دعوة قوية للتقديم الفوري.

الأسلوب:
- عربية فصحى راقية ومقنعة.
- 300-500 كلمة.
- استخدم لغة تحفيزية للطلاب الفلسطينيين.
- لا تضف نصائح شخصية أو حوار - وصف فقط.
PROMPT;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
    }

    public function generateScholarshipDescription(string $title): string
    {
        if (empty($this->apiKey)) {
            return '⚠️ مفتاح Groq API غير مضبوط.';
        }

        $userPrompt = "اكتب وصفاً جذاباً واحترافياً لمنحة دراسية بعنوان: '$title'. اتبع القواعد بدقة.";

        $payloadMessages = [
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => $payloadMessages,
                'temperature' => 0.6,
                'max_tokens' => 2048,
            ]);

            if (!$response->successful()) {
                Log::error('Groq Scholarship API error: ' . $response->body());
                return 'عذراً، حدث خطأ في الاتصال بالذكاء الاصطناعي.';
            }

            return $response->json('choices.0.message.content', '');

        } catch (\Exception $e) {
            Log::error('Groq Scholarship exception: ' . $e->getMessage());
            return 'خطأ تقني، يرجى المحاولة لاحقاً.';
        }
    }

    public function generateAllSections(string $title, string $university, string $country, string $category): array
    {
        if (empty($this->apiKey)) {
            return ['error' => '⚠️ مفتاح Groq API غير مضبوط.'];
        }

        // تم تعديل الـ Prompt هنا لمنع الانهيار وضمان تغليف الـ Markdown بـ Valid JSON String
        $systemPrompt = <<<'PROMPT'
أنت مساعد ذكي ومتخصص في كتابة محتوى المنح الدراسية. 
يجب أن تكون إجابتك عبارة عن كائن JSON صلب ونظيف ومغلق تماماً بشكل صحيح (Valid JSON Object).

شروط صارمة للبنية الهيكلية:
- القيمة المقترنة بكل مفتاح يجب أن تبدأ وتنتهي بعلامات تنصيص مزدوجة وتكون عبارة عن نص صريح (String).
- بالنسبة للنقاط (Markdown)، اكتبها كنص عادي داخل علامات التنصيص للمفتاح، واستخدم رمز الـ `\n` للانتقال لسطر جديد. لا تضع أي رموز تنقيط أو أسطر جديدة خارج علامات التنصيص الخاصة بالقيم النصية نهائياً.

هيكل الـ JSON المطلوب بدقة:
{
  "description": "وصف عام ومختصر وشامل للمنحة (سيرتين أو ثلاثة) ليوضع كخلاصة للمنحة",
  "overview": "نظرة عامة عن المنحة والجامعة والبلد بأسلوب جذاب للطلاب الفلسطينيين (100-150 كلمة)",
  "conditions": "- الشرط الأول\\n- الشرط الثاني\\n- الشرط الثالث",
  "documents": "- المستند الأول\\n- المستند الثاني",
  "features": "- الميزة الأولى\\n- الميزة الثانية",
  "application_process": "1. الخطوة الأولى\\n2. الخطوة الثانية\\n3. الخطوة الثالثة (اشرح آلية التقديم على المنحة خطوة بخطوة بشكل عملي وواضح)"
}
PROMPT;

        $userPrompt = "قم بتوليد بيانات منحة باللغة العربية لـ '$title' في جامعة '$university'، '$country'، لتصنيف '$category'.";

        $payloadMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(45)->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => $payloadMessages,
                'temperature' => 0.4, // تم خفض الـ temperature قليلاً لزيادة الالتزام بالقواعد
                'max_tokens' => 4096,
                'response_format' => ['type' => 'json_object'] 
            ]);

            if (!$response->successful()) {
                Log::error('Groq Sections API error: ' . $response->body());
                return ['error' => 'خطأ في الاتصال بالذكاء الاصطناعي.'];
            }

            $rawContent = $response->json('choices.0.message.content', '');
            
            $parsedJson = json_decode($rawContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Groq JSON parsing error: ' . json_last_error_msg());
                Log::error('Raw content was: ' . $rawContent);
                return ['error' => 'فشل في تحليل البيانات الراجعة من الذكاء الاصطناعي.'];
            }

            return [
                'description' => $parsedJson['description'] ?? '',
                'overview'   => $parsedJson['overview'] ?? '',
                'conditions' => $parsedJson['conditions'] ?? '',
                'documents'  => $parsedJson['documents'] ?? '',
                'features'   => $parsedJson['features'] ?? '',
                'application_process' => $parsedJson['application_process'] ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('Groq Sections exception: ' . $e->getMessage());
            return ['error' => 'خطأ تقني: ' . $e->getMessage()];
        }
    }

    /**
     * يقرأ نص خام (منسوخ من أي مصدر: موقع الجامعة، PDF، رسالة...) ويوزّعه على
     * كل حقول نموذج المنحة دفعة وحدة، بما فيها الحقول الوصفية (العنوان،
     * الجامعة، الموعد النهائي...) وليس فقط الأقسام الست النصية.
     */
    public function extractFromRawText(string $rawText): array
    {
        if (empty($this->apiKey)) {
            return ['error' => '⚠️ مفتاح Groq API غير مضبوط.'];
        }

        $systemPrompt = <<<'PROMPT'
أنت مساعد إدخال بيانات متخصص في المنح الدراسية. ستُعطى نصاً خاماً غير منظم (قد يكون
منسوخاً من موقع جامعة، ملف PDF، أو رسالة) يصف منحة دراسية واحدة، ومهمتك قراءته
واستخراج كل معلومة ممكنة منه، ثم توزيعها على حقول JSON محددة بدقة.

يجب أن يكون ردك عبارة عن كائن JSON صلب صحيح تماماً (Valid JSON Object) فقط، بدون أي
نص خارج علامات { }.

قواعد صارمة:
- إذا معلومة غير موجودة بالنص الخام إطلاقاً، استخدم قيمة فارغة "" أو null (حسب نوع
  الحقل) - لا تختلق بيانات غير موجودة أبداً (لا تخترع تاريخاً أو رقماً غير مذكور).
- categories: مصفوفة، القيم المسموحة فقط: "Bachelor","Master","PhD","Short Course".
- coverage: مصفوفة، القيم المسموحة فقط: "تمويل كامل","إعفاء من الرسوم","راتب شهري","تأمين صحي","تذاكر طيران","سكن جامعي".
- tags: مصفوفة، القيم المسموحة فقط: "هندسة","طب","تقنية","إدارة","علوم","فن".
- deadline: بصيغة YYYY-MM-DD فقط إذا وُجد تاريخ صريح بالنص. إذا كانت هناك عدة مواعيد
  ضمن مراحل التقديم، استخدم آخر موعد نهائي (أقرب موعد إغلاق فعلي للتقديم). وإلا اتركه null.
- min_gpa: رقم من 0 إلى 100 (نسبة مئوية) فقط - هذا الحقل بمقياس 0-100 حصراً، وليس بمقياس
  GPA من 4 أو 5. إذا ذكر النص حد أدنى بصيغة نسبة مئوية أو معدل ثانوية عامة (مثال: "80%"
  أو "معدل لا يقل عن 85") استخدمه مباشرة. إذا ذكر النص حد أدنى بمقياس مختلف (مثال: GPA
  3.7 من 4.0) حوّله لنسبة مئوية بقسمته على الحد الأقصى المذكور وضربه في 100 (3.7/4.0×100 = 92.5).
  إذا كان مقياس الرقم المذكور غير واضح إطلاقاً، اتركه null بدل تخمين رقم خاطئ.
- financial_value: نص قصير (أقل من 15 كلمة) يلخص التغطية المالية، مبني على المعلومات
  الفعلية بالنص (وليس اختلاقاً) - لخّص ما ذُكر عن التمويل/السكن/الراتب حتى لو لم يكن
  بالنص جملة واحدة جاهزة تصفه، طالما المعلومات موجودة ومُشتقة من نفس النص فقط.
- recommended_tags: نص واحد قصير، كلمات مفصولة بفاصلة (مثال: "ممولة كاملاً, هندسة, بدون آيلتس").
- الحقول description/overview/conditions/documents/features/application_process يجب أن
  تكون HTML صحيح وجاهز للعرض مباشرة (لا نص Markdown ولا \n)، بنفس أسلوب التنسيق التالي بالضبط:

  description: فقرة "<p>" واحدة مختصرة (سطرين إلى ثلاثة) تلخص المنحة.

  overview: فقرات "<h4>" لعناوين فرعية مرقّمة (مثال: "<h4>1. المنح التنافسية</h4>")
  تتبعها "<p>" شرح، وإن وُجدت أمثلة استخدم "<p><strong>أبرز الأمثلة:</strong></p>"
  متبوعة بقائمة "<ul><li>🔹 ...</li></ul>".

  conditions: قائمة "<ul><li>" فقط، كل عنصر يبدأ بـ "✅ " ثم "<strong>عنوان الشرط:</strong> "
  ثم التفاصيل. مثال: "<li>✅ <strong>الوضع الأكاديمي:</strong> أن يكون الطالب في السنة
  الأخيرة من الثانوية العامة.</li>".

  documents: قائمة "<ul><li>" فقط، كل عنصر يبدأ بـ "✅ " ثم اسم المستند مباشرة (بدون
  عنوان بولد). مثال: "<li>✅ شهادة الثانوية العامة وكشف الدرجات</li>".

  features: قائمة "<ul><li>" فقط، كل عنصر يبدأ برمز تعبيري مناسب لمحتواه (🎁 للتمويل،
  🏠 للسكن، 📚 للكتب، 💳 للرسوم، 🏆 للمكانة الأكاديمية، أو رمز مناسب آخر) ثم
  "<strong>عنوان الميزة:</strong> " ثم التفاصيل.

  application_process: عناوين "<h4>" مرقّمة لكل خطوة (مثال: "<h4>1. الترشيح المدرسي</h4>")
  تتبعها "<p>" شرح عملي وواضح للخطوة، مع "<strong>" لأي ملاحظة مهمة داخل الشرح.

هيكل الـ JSON المطلوب بدقة (كل المفاتيح إلزامية حتى لو فارغة):
{
  "title_ar": "", "title_en": "", "country": "", "university": "",
  "deadline": null, "financial_value": "", "min_gpa": null, "applicants_count": null,
  "recommended_tags": "", "application_url": "", "apply_via_us_link": "",
  "categories": [], "coverage": [], "tags": [],
  "description": "", "overview": "", "conditions": "", "documents": "",
  "features": "", "application_process": ""
}
PROMPT;

        $userPrompt = "اقرأ النص الخام التالي عن منحة دراسية، واستخرج منه كل حقل ممكن حسب الهيكل المطلوب:\n\n" . $rawText;

        $payloadMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => $payloadMessages,
                'temperature' => 0.3,
                'max_tokens' => 6000,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                Log::error('Groq Extract API error: ' . $response->body());
                return ['error' => 'خطأ في الاتصال بالذكاء الاصطناعي.'];
            }

            $rawContent = $response->json('choices.0.message.content', '');
            $parsed = json_decode($rawContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Groq Extract JSON parsing error: ' . json_last_error_msg());
                Log::error('Raw content was: ' . $rawContent);
                return ['error' => 'فشل في تحليل البيانات الراجعة من الذكاء الاصطناعي.'];
            }

            $allowedCategories = ['Bachelor', 'Master', 'PhD', 'Short Course'];
            $allowedCoverage = ['تمويل كامل', 'إعفاء من الرسوم', 'راتب شهري', 'تأمين صحي', 'تذاكر طيران', 'سكن جامعي'];
            $allowedTags = ['هندسة', 'طب', 'تقنية', 'إدارة', 'علوم', 'فن'];

            return [
                'title_ar' => $parsed['title_ar'] ?? '',
                'title_en' => $parsed['title_en'] ?? '',
                'country' => $parsed['country'] ?? '',
                'university' => $parsed['university'] ?? '',
                'deadline' => $parsed['deadline'] ?? null,
                'financial_value' => $parsed['financial_value'] ?? '',
                'min_gpa' => $parsed['min_gpa'] ?? null,
                'applicants_count' => $parsed['applicants_count'] ?? null,
                'recommended_tags' => $parsed['recommended_tags'] ?? '',
                'application_url' => $parsed['application_url'] ?? '',
                'apply_via_us_link' => $parsed['apply_via_us_link'] ?? '',
                'categories' => array_values(array_intersect((array) ($parsed['categories'] ?? []), $allowedCategories)),
                'coverage' => array_values(array_intersect((array) ($parsed['coverage'] ?? []), $allowedCoverage)),
                'tags' => array_values(array_intersect((array) ($parsed['tags'] ?? []), $allowedTags)),
                'description' => $parsed['description'] ?? '',
                'overview' => $parsed['overview'] ?? '',
                'conditions' => $parsed['conditions'] ?? '',
                'documents' => $parsed['documents'] ?? '',
                'features' => $parsed['features'] ?? '',
                'application_process' => $parsed['application_process'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('Groq Extract exception: ' . $e->getMessage());
            return ['error' => 'خطأ تقني: ' . $e->getMessage()];
        }
    }
}