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
  "features": "- الميزة الأولى\\n- الميزة الثانية"
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
                'features'   => $parsedJson['features'] ?? ''
            ];

        } catch (\Exception $e) {
            Log::error('Groq Sections exception: ' . $e->getMessage());
            return ['error' => 'خطأ تقني: ' . $e->getMessage()];
        }
    }
}