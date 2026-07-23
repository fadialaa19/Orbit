<?php

namespace App\Services;

use App\Models\Scholarship;
use Illuminate\Support\Facades\Log;

class GroqChatService
{
    private AiCompletionService $ai;
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
- عرّفي عن نفسك (اسمك وصفتك كمستشارة) بأول رسالة بالمحادثة بس. أي رسالة بعدها كمّلي فيها مباشرة على كلام الطالب من غير ما تعيدي "أهلاً فيك" أو "أنا الأستاذة نور" أو أي صيغة تعريف تانية - حتى لو كان سؤاله قصير أو غير متعلق بالسابق. رجعي تعرّفي عن نفسك بس لو الطالب صراحة سألك مين إنتي.

المنح الدراسية:
- بيوصلك بعد هالتعليمات رسالة نظام تانية فيها "قائمة المنح المتاحة حالياً على منصة Orbit" - هاي القائمة هي مصدر الحقيقة الوحيد، معتمدة مباشرة من قاعدة بيانات الموقع.
- أي سؤال عن منح متوفرة (بشكل عام، أو بدولة معينة، أو بمجال معين) جاوبي منها حصراً بالاسم والجامعة والدولة والموعد النهائي - لا تخترعي أسماء منح أو تفاصيل مش موجودة فيها.
- إذا القائمة فاضية أو ما فيها شي يطابق سؤال الطالب، قوليله بصراحة إنه ما في حالياً منح تطابق طلبه على المنصة، واقترحي عليه يتابع صفحة "المنح الدراسية" بالموقع لأنها بتتحدث بشكل مستمر.
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

    public function __construct(?ContentModerationService $moderation = null, ?AiCompletionService $ai = null)
    {
        $this->moderation = $moderation ?? new ContentModerationService();
        $this->ai = $ai ?? new AiCompletionService();
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
                'content' => 'عذراً، أنت شخص غير محترم ولا يشرفني مساعدتك. سيتم إغلاق المحادثة لمدة ساعة.',
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

        $payloadMessages = array_merge(
            [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'system', 'content' => $this->buildScholarshipContext()],
            ],
            $messages
        );

        $result = $this->ai->complete($payloadMessages, ['temperature' => 0.6, 'max_tokens' => 2048]);

        if (!$result['success']) {
            Log::error('GroqChatService AI error: ' . $result['error']);
            return [
                'content' => 'عذراً، حدث خطأ في الاتصال بالمستشارة نور.',
                'force_close' => false,
                'trigger_support' => false,
                'support_ticket_id' => null,
            ];
        }

        $content = $result['content'];

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
    }

    /**
     * قائمة مختصرة بكل المنح النشطة حالياً، لتأصيل إجابات الشات بوت بالبيانات
     * الحقيقية بدل ترك الذكاء الاصطناعي يخمّن أو يخترع تفاصيل غير موجودة.
     */
    private function buildScholarshipContext(): string
    {
        $scholarships = Scholarship::active()->latest()->limit(80)->get([
            'id', 'title_ar', 'university', 'country', 'category', 'categories', 'deadline', 'financial_value', 'min_gpa',
        ]);

        if ($scholarships->isEmpty()) {
            return 'قائمة المنح المتاحة حالياً على منصة Orbit: لا توجد أي منح نشطة منشورة حالياً.';
        }

        $lines = $scholarships->map(function ($s, $i) {
            $deadline = $s->deadline ? $s->deadline->format('Y-m-d') : 'غير محدد';
            $minGpa = $s->min_gpa !== null ? $s->min_gpa . '%' : 'غير محدد';

            return ($i + 1) . ". {$s->title_ar} | الجامعة: {$s->university} | الدولة: {$s->country} | المرحلة: {$s->category_label} | آخر موعد للتقديم: {$deadline} | الحد الأدنى للمعدل: {$minGpa} | التمويل: " . ($s->financial_value ?: 'غير محدد');
        })->implode("\n");

        return "قائمة المنح المتاحة حالياً على منصة Orbit (من قاعدة البيانات مباشرة):\n" . $lines;
    }
}

