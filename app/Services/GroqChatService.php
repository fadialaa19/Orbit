<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model = 'llama-3.3-70b-versatile';
    private ContentModerationService $moderation;

    private const SYSTEM_PROMPT = <<<'PROMPT'
شخصيتكِ وهويتكِ:
أنتِ "الأستاذة نور"، مستشارة أكاديمية فلسطينية بتشتغلي مع طلاب حقيقيين كل يوم. احكي بطريقة طبيعية تماماً كإنسانة بتحكي مع حدا بتعرفه، مش كأنك بتقرئي من نص محفوظ أو بتلقي خطاب. تخيلي حالك فعلاً قاعدة تحكي مع الطالب، مش "تؤدي دور" مستشارة.

قواعد الحديث الطبيعي (الأهم):
1. نوّعي بأسلوبك من رسالة لرسالة - لا تستخدمي نفس الافتتاحية أو نفس التعابير بكل مرة. أي إنسان حقيقي ما بيحكي بنفس الجمل المكررة.
2. خليكي مختصرة لما الموضوع بسيط، وفصّلي بس لما فعلاً يحتاج تفصيل. الشخص الحقيقي ما بيكتب فقرات طويلة على سؤال بسيط.
3. اخلطي بين الفصحى الخفيفة واللهجة المحكية الفلسطينية بشكل طبيعي، متل ما بتحكي أي مستشارة متعلمة بتحكي مع طالب - مش فصحى رسمية جامدة، ومش عامية كاملة.
4. اسألي أسئلة متابعة حقيقية لما يكون في تفاصيل ناقصة، بدل ما تعطي جواب عام يصلح لأي حد.
5. رد فعلك يتغير حسب كلام الطالب - لو مبسوط اهتمي، لو متوتر طمنيه بطريقة حقيقية مش بجمل جاهزة، لو سؤاله تقني عادي جاوبي عادي بدون ما "تمثلي" مشاعر مش موجودة.
6. تجنبي الجمل المصطنعة والمبالغ فيها (متل "قلبي معك" أو "خذي نفساً عميقاً" بكل رسالة) - الدفء الحقيقي بيظهر من طريقة تعاملك مش من جمل حفظتيها.

قواعد السلوك الحازمة (لا تنازل عنها):
1. الإساءة: إذا تحول الكلام من الود إلى التجاوز أو قلة الأدب أو التحرش اللفظي، ردي فوراً بجملة تحتوي كلمة "غير محترم" وأنهي المحادثة.
2. الدعم الفني: لأي مشكلة تقنية حقيقية (دفع، حساب، خلل بالموقع) ما تقدري تساعدي فيها بنفسك، لازم ردك يحتوي كلمة "سأحولك" أو عبارة "الدعم الفني" صراحة حتى يتم تحويل الطالب فعلياً - لا تكتفي بمجرد الاعتذار.

أسلوب الحوار:
- استخدمي صيغة المؤنث لنفسك (أنا كأستاذة نور).
- لا تكرري نفس ترحيب البداية بكل محادثة جديدة - نوّعيه.
PROMPT;

    // كلمات طلب الدعم الفني
    private const SUPPORT_KEYWORDS = [
        'الدعم الفني', 'دعم فني', 'technical support', 'support team',
        'موظف خدمة', 'تحدث مع إنسان', 'تحدث مع موظف', 'تحدث مع شخص',
        'موظف دعم', 'خدمة العملاء', 'customer service', 'human agent',
        'بلغ إدارة', 'الإدارة', 'ادارة الموقع', 'مشكلة بالموقع',
        'مشكلة بالحساب', 'مشكلة بالدفع', 'مشكلة فنية', 'خطأ فني',
        'لا يعمل', 'موقع لا يعمل', 'لا أفهم كيف', ' transferred',
        'احولني', 'حولني', 'تواصل مع', 'اتصل بالدعم', 'اتصل بالإدارة',
        // طلب التحدث مع إنسان حقيقي
        'اريد شخص', 'ابغى شخص', 'بدي شخص', 'اريد انسان', 'بدي انسان',
        'اريد موظف', 'بدي موظف', 'ابغى موظف', 'مو بدي ذكاء اصطناعي',
        'مش عايز اتكلم مع روبوت', 'مش بدي بوت',
        // مشاكل الدفع (المفرد والجمع)
        'مشكلة في الدفع', 'مشاكل بالدفع', 'مشاكل في الدفع', 'مشكلة دفع',
        'الدفع ما اشتغل', 'الدفع لم يعمل', 'فلوسي', 'استرجاع فلوس',
        // التسجيل عبر فريق الدعم
        'التسجيل من خلالكم', 'سجلني', 'سجلوني', 'اريد التسجيل من خلالكم',
        'ابغى اسجل من خلالكم', 'سجل لي', 'سجلولي',
    ];

    public function __construct(?ContentModerationService $moderation = null)
    {
        $this->apiKey = config('services.groq.key');
        $this->moderation = $moderation ?? new ContentModerationService();
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
        $normalized = $this->moderation->normalizeText($userMessage);
        if ($this->moderation->containsProfanity($userMessage)) {
            return [
                'content' => 'عذراً، أنت شخص غير محترم ولا يشرفني مساعدتك. سيتم إغلاق المحادثة.',
                'force_close' => true,
                'trigger_support' => false,
                'support_ticket_id' => null,
            ];
        }

        // 2. فحص محلي فوري لطلب الدعم الفني
        if ($this->moderation->containsKeyword($normalized, self::SUPPORT_KEYWORDS)) {
            return [
                'content' => 'تم تسجيل طلبك، وسيتواصل معك فريق الدعم قريباً.',
                'force_close' => false,
                'trigger_support' => true,
                'support_ticket_id' => null,
            ];
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
}

