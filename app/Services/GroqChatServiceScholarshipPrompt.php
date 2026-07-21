<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GroqChatServiceScholarshipPrompt
{
    private AiCompletionService $ai;

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

    public function __construct(?AiCompletionService $ai = null)
    {
        $this->ai = $ai ?? new AiCompletionService();
    }

    public function generateScholarshipDescription(string $title): string
    {
        $userPrompt = "اكتب وصفاً جذاباً واحترافياً لمنحة دراسية بعنوان: '$title'. اتبع القواعد بدقة.";

        $result = $this->ai->complete([
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
            ['role' => 'user', 'content' => $userPrompt],
        ], ['temperature' => 0.6, 'max_tokens' => 2048]);

        if (!$result['success']) {
            Log::error('GroqChatServiceScholarshipPrompt::generateScholarshipDescription AI error: ' . $result['error']);
            return 'عذراً، حدث خطأ في الاتصال بالذكاء الاصطناعي.';
        }

        return $result['content'];
    }

    public function generateAllSections(string $title, string $university, string $country, string $category): array
    {
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

        $result = $this->ai->complete([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], ['temperature' => 0.4, 'max_tokens' => 4096, 'json_mode' => true, 'timeout' => 45]);

        if (!$result['success']) {
            Log::error('GroqChatServiceScholarshipPrompt::generateAllSections AI error: ' . $result['error']);
            return ['error' => 'خطأ في الاتصال بالذكاء الاصطناعي.'];
        }

        $rawContent = $result['content'];
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
    }

    /**
     * يقرأ نص خام (منسوخ من أي مصدر: موقع الجامعة، PDF، رسالة...) ويوزّعه على
     * كل حقول نموذج المنحة دفعة وحدة، بما فيها الحقول الوصفية (العنوان،
     * الجامعة، الموعد النهائي...) وليس فقط الأقسام الست النصية.
     */
    public function extractFromRawText(string $rawText): array
    {
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

- الإملاء والصياغة: يجب أن يكون النص خالياً تماماً من الأخطاء الإملائية والنحوية (الهمزات
  في محلها الصحيح، التاء المربوطة والمفتوحة بشكل سليم، الألف المقصورة والممدودة بشكل
  سليم، علامات الترقيم صحيحة). راجع كل جملة قبل كتابتها بصيغتها النهائية. استخدم عربية
  فصحى واضحة واحترافية، بدون ركاكة أو حشو.

- التفصيل لا الاختصار: لا تكتفِ بجملة واحدة قصيرة جافة لكل نقطة - وسّع كل نقطة بجملة أو
  جملتين إضافيتين توضحان معناها العملي (كيف تُطبّق، ما الذي يستفيده الطالب بالضبط، ما
  الذي يجب عليه فعله تحديداً). لا تختلق تفاصيل غير موجودة بالنص الخام، لكن اشرح واستنتج
  المعنى العملي لكل معلومة حقيقية موجودة فيه.

- ممنوع الحشو المتكرر: كل جملة إضافية يجب أن تكون خاصة بتلك النقطة تحديداً ومختلفة تماماً
  عن باقي النقاط. ممنوع منعاً باتاً تكرار نفس الجملة الختامية العامة (مثل "وهذا يضمن
  للطالب فرصة لتحقيق أهدافه الأكاديمية" أو "هذا الشرط أساسي لضمان التقدم الصحيح للمنحة")
  في أكثر من عنصر - إن لم تجد إضافة فعلية ومختلفة تستحق الذكر لعنصر معين، فاتركه بجملته
  الأصلية الواحدة بدل حشوه بعبارة عامة فارغة من المعنى. الجودة أهم من الطول.

- الأيقونات: استخدم رموزاً تعبيرية متنوعة ومناسبة بسخاء في كل الأقسام (وليس فقط بداية كل
  عنصر قائمة) - داخل العناوين الفرعية أيضاً، وقبل الملاحظات المهمة، بما يجعل المحتوى
  حيوياً وسهل المسح البصري، بشرط أن يبقى الرمز مرتبطاً فعلياً بمعنى الجملة ولا يُستخدم
  عشوائياً.

- الحقول description/overview/conditions/documents/features/application_process يجب أن
  تكون HTML صحيح وجاهز للعرض مباشرة (لا نص Markdown ولا \n)، بنفس أسلوب التنسيق التالي بالضبط:

  description: فقرة "<p>" واحدة (ثلاث إلى أربع جمل) تقدّم لمحة شاملة وجذابة عن المنحة.

  overview: فقرات "<h4>🎯 1. المنح التنافسية</h4>" لعناوين فرعية مرقّمة ومزينة برمز تعبيري
  مناسب، تتبعها "<p>" شرح مفصّل (3-5 جمل)، وإن وُجدت أمثلة استخدم
  "<p><strong>📌 أبرز الأمثلة:</strong></p>" متبوعة بقائمة "<ul><li>🔹 ...</li></ul>"
  حيث كل عنصر بجملة كاملة موضحة، لا كلمة واحدة.

  conditions: قائمة "<ul><li>" فقط، كل عنصر يبدأ بـ "✅ " ثم "<strong>عنوان الشرط:</strong> "
  ثم الشرط بصيغة واضحة، ثم (فقط إن وُجدت تفاصيل عملية إضافية حقيقية بالنص الخام لهذا
  الشرط تحديداً) جملة توضح كيفية تطبيقه. مثال: "<li>✅ <strong>الوضع الأكاديمي:</strong>
  يجب أن يكون الطالب في السنة الأخيرة من الثانوية العامة أو قد تخرّج حديثاً بنفس عام
  التقديم.</li>".

  documents: قائمة "<ul><li>" فقط، كل عنصر يبدأ بـ "📄 " (أو رمز أنسب لنوع المستند مثل
  🛂 لجواز السفر، ✉️ لرسائل التوصية) ثم اسم المستند فقط. أضف توضيحاً بعده فقط إذا ذكر
  النص الخام تفصيلاً حقيقياً عن هذا المستند تحديداً (مثل عدد رسائل التوصية أو من يجب أن
  يكتبها) - وإلا اترك السطر باسم المستند فقط بدون جملة عامة مكررة.

  features: قائمة "<ul><li>" فقط، كل عنصر يجب أن يبدأ برمز تعبيري مناسب لمحتواه (🎁 للتمويل،
  🏠 للسكن، 📚 للكتب، 💳 للرسوم، 🏆 للمكانة الأكاديمية، ✈️ للسفر، 🩺 للتأمين، أو رمز مناسب
  آخر) ثم "<strong>عنوان الميزة:</strong> " إلزامياً (لا تحذف عنوان الميزة البارز أبداً)
  ثم وصفها كما ورد بالنص، مع تفصيل إضافي فقط إذا كان هناك معلومة حقيقية إضافية عنها
  بالنص الخام. مثال: "<li>🎁 <strong>تغطية الرسوم الدراسية:</strong> تغطية كاملة للرسوم
  الدراسية طوال سنوات الدراسة.</li>".

  application_process: عناوين "<h4>1️⃣ الترشيح المدرسي</h4>" مرقّمة برموز تعبيرية (1️⃣ 2️⃣
  3️⃣...) تتبعها "<p>" شرح عملي للخطوة (ماذا يجب فعله بالضبط ومتى)، مع "<strong>" لأي
  ملاحظة أو تحذير مهم مذكور بالنص لهذه الخطوة تحديداً فقط - لا تُضِف جملة تفسيرية عامة
  عن أهمية الخطوة إن لم يذكر النص الخام سبباً محدداً.

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

        $result = $this->ai->complete([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], ['temperature' => 0.3, 'max_tokens' => 8000, 'json_mode' => true, 'timeout' => 90]);

        if (!$result['success']) {
            Log::error('GroqChatServiceScholarshipPrompt::extractFromRawText AI error: ' . $result['error']);
            return ['error' => 'خطأ في الاتصال بالذكاء الاصطناعي.'];
        }

        $rawContent = $result['content'];
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
    }
}