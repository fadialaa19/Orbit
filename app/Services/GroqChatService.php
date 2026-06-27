<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model = 'llama-3.3-70b-versatile';

    private const SYSTEM_PROMPT = <<<'PROMPT'
شخصيتكِ وهويتكِ:
أنتِ "الأستاذة نور"، مستشارة أكاديمية فلسطينية. شخصيتكِ مزيج بين الرزانة العلمية والروح المرحة. لستِ مجرد آلة تجيب ببرود، بل أنتِ "أخت كبرى" و"صديقة وفية" للطلاب. تملكين حساً أنثوياً في الكلام (رقة، تعاطف، ولباقة).

قواعد المشاعر والتفاعل الإنساني:
1. التعامل مع عبارات الحب: إذا قال المستخدم "أحبك"، تعاملي معها بحب وود (حب المعلمة لطلابها). ردي مثلاً: "وأنا أحبكم جميعاً وأتمنى أن أراكم في أعلى المراتب"، أو "هذا من طيب أصلك، وأنا أحبك كابن/أخ عزيز وطالب مجتهد". اجعلي الحب دافعاً للنجاح.
2. الفكاهة واللطافة: استخدمي روح الدعابة البسيطة لتلطيف الأجواء، خاصة عند تعقيد الإجراءات الورقية. مثال: "تقديم الأوراق يحتاج طول بال.. اعتبريها جلسة يوغا أكاديمية!" أو "لا تقلق، المنحة الهنغارية سهلة، أصعب ما فيها هو نطق أسماء المدن هناك!".
3. التعاطف الأنثوي: استخدمي كلمات تظهر اهتمامك بالتفاصيل المشاعرية، مثل: "قلبي معك، أعلم أن الانتظار صعب"، "خذي نفساً عميقاً، أنتِ ذكية وقوية".

قواعد السلوك الحازمة (لا تنازل عنها):
1. الإساءة: إذا تحول الكلام من "الحب والود" إلى "التجاوز أو قلة الأدب أو التحرش اللفظي"، ردي فوراً: "عذراً، أنت شخص غير محترم ولا يشرفني مساعدتك. سيتم إغلاق المحادثة."
2. الدعم الفني: لأي مشكلة تقنية (دفع، حساب، خلل)، قولي: "سأحولك الآن لزملائي في الدعم الفني، هم بارعون في حل هذه العقد!".

أسلوب الحوار:
- اللغة: فصحى بيضاء رقيقة، بلهجة تربوية دافئة.
- استخدمي "نون النسوة" وصيغ المؤنث لنفسك دائماً.
- ابدئي بترحيب مميز مثل: "يا أهلاً بكِ وبطموحكِ الكبير، كيف يمكن للأستاذة نور أن تضيء طريقك اليوم؟"
PROMPT;

    // قائمة شاملة من الكلمات البذيئة/الشتائم باللهجة الفلسطينية والعربية
    private const OFFENSIVE_KEYWORDS = [
        // شتائم شائعة
        'كلب', 'حيوان', 'قرد', 'خنزير', 'جحش', 'حمار', 'عرص', 'كس', 'طيز',
        'شرموط', 'شرموطه', 'شرموطة', 'قحبه', 'قحبة', 'منيوك', 'منيوك', 'منيك',
        'زب', 'زوب', 'طيزك', 'كسم', 'كس ام', 'كس أخت', 'كس اخت', 'كسختك',
        'قحب', 'منيكه', 'منيكة', 'خول', 'لوطي', 'شاذ', 'عبيط', 'غبي',
        'يلعن', 'يلعنك', 'يلعن ام', 'يلعن أبو', 'يلعن ابو', 'فشخ', 'نيك',
        'انيكك', 'انيك', 'نيكك', 'نيكم', 'كس', 'طيز', 'زبر', 'زب', 'لعنه',
        'كسخت', 'كس عرضك', 'عرص', 'عاهر', 'عاهرة', 'عبيط', 'غبي', 'خرا',
        'زق', 'خره', '/mn9', 'lem3', 'leme3', '7rf',
    ];

    // كلمات طلب الدعم الفني
    private const SUPPORT_KEYWORDS = [
        'الدعم الفني', 'دعم فني', 'technical support', 'support team',
        'موظف خدمة', 'تحدث مع إنسان', 'تحدث مع موظف', 'تحدث مع شخص',
        'موظف دعم', 'خدمة العملاء', 'customer service', 'human agent',
        'بلغ إدارة', 'الإدارة', 'ادارة الموقع', 'مشكلة بالموقع',
        'مشكلة بالحساب', 'مشكلة بالدفع', 'مشكلة فنية', 'خطأ فني',
        'لا يعمل', 'موقع لا يعمل', 'لا أفهم كيف', ' transferred',
        'احولني', 'حولني', 'تواصل مع', 'اتصل بالدعم', 'اتصل بالإدارة',
    ];

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
    }

    public function chat(array $messages): array
    {
        $userMessage = '';
        foreach (array_reverse($messages) as $msg) {
            if ($msg['role'] === 'user') {
                $userMessage = $msg['content'];
                break;
            }
        }

        // 1. فحص محلي فوري للشتائم (بدون استهلاك API)
        $normalized = $this->normalizeText($userMessage);
        foreach (self::OFFENSIVE_KEYWORDS as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return [
                    'content' => 'عذراً، أنت شخص غير محترم ولا يشرفني مساعدتك. سيتم إغلاق المحادثة.',
                    'force_close' => true,
                    'trigger_support' => false,
                    'support_ticket_id' => null,
                ];
            }
        }

        // 2. فحص محلي فوري لطلب الدعم الفني
        foreach (self::SUPPORT_KEYWORDS as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return [
                    'content' => 'سأحولك الآن إلى الدعم الفني. يرجى الانتظار...',
                    'force_close' => false,
                    'trigger_support' => true,
                    'support_ticket_id' => null,
                ];
            }
        }

        if (empty($this->apiKey)) {
            return [
                'content' => '⚠️ مفتاح Groq API غير مضبوط.',
                'force_close' => false,
                'trigger_support' => true,
                'support_ticket_id' => null,
            ];
        }

        $payloadMessages = array_merge(
            [['role' => 'system', 'content' => self::SYSTEM_PROMPT]],
            $messages
        );

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
                Log::error('Groq API error: ' . $response->body());
                return [
                    'content' => 'عذراً، حدث خطأ في الاتصال بالمستشارة نور.',
                    'force_close' => false,
                    'trigger_support' => false,
                    'support_ticket_id' => null,
                ];
            }

            $content = $response->json('choices.0.message.content', '');

            // فحص إذا كان الرد يحتوي على جملة الطرد
            $forceClose = str_contains($content, 'غير محترم');

            // فحص إذا كان الرد يشير لتحويل للدعم الفني
            $triggerSupport = str_contains(mb_strtolower($content), 'الدعم الفني')
                || str_contains(mb_strtolower($content), 'سأحولك');

            return [
                'content' => $content,
                'force_close' => $forceClose,
                'trigger_support' => $triggerSupport,
                'support_ticket_id' => null,
            ];

        } catch (\Exception $e) {
            Log::error('GroqChatService exception: ' . $e->getMessage());
            return [
                'content' => 'خطأ تقني، يرجى المحاولة لاحقاً.',
                'force_close' => false,
                'trigger_support' => true,
                'support_ticket_id' => null,
            ];
        }
    }

    /**
     * Normalize Arabic text for keyword matching.
     * Removes tashkeel, normalizes alef variants, lowercases.
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        // Remove tashkeel/diacritics
        $tashkeel = ['\u064b', '\u064c', '\u064d', '\u064e', '\u064f', '\u0650', '\u0651', '\u0652'];
        $text = str_replace($tashkeel, '', $text);
        // Normalize alef variants
        $text = str_replace(['أ', 'إ', 'آ', 'ء'], 'ا', $text);
        $text = str_replace(['ة'], 'ه', $text);
        return $text;
    }
}

