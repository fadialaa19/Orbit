<?php

namespace App\Services;

use App\Services\GroqChatService;

class BedoonAIContextService
{
    private GroqChatService $groq;

    public function __construct(GroqChatService $groq)
    {
        $this->groq = $groq;
    }

    public function chatWithContext(array $messages, array $context = [])
    {
        $systemPrompt = $this->buildContextPrompt($context);
        $messagesWithContext = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        return $this->groq->chat(array_merge($messagesWithContext, $messages));
    }

    private function buildContextPrompt(array $context): string
    {
        $prompt = "سياق إضافي مهم:\n";

        if (isset($context['current_page'])) {
            $prompt .= "- الصفحة الحالية: " . $context['current_page'] . "\n";
        }

        if (isset($context['chat_type'])) {
            $prompt .= "- نوع المحادثة: " . $context['chat_type'] . "\n";
        }

        if (isset($context['room_topic'])) {
            $prompt .= "- موضوع الغرفة: " . $context['room_topic'] . "\n";
        }

        if (isset($context['scholarship_id'])) {
            $prompt .= "- منحة محددة: ID " . $context['scholarship_id'] . "\n";
        }

        $prompt .= "\nقدم ردوداً مخصصة بناءً على هذا السياق. إذا كان المستخدم في صفحة معينة، اربط الإجابة بها.";

        return $prompt;
    }

    public function suggestAction(string $userMessage, array $context): ?string
    {
        $prompt = "تحليل الرسالة التالية وحدد إذا كانت تحتوي على طلب عمل محدد. السياق: " . json_encode($context) . "\n\nالرسالة: " . $userMessage . "\n\nإذا كان طلب عمل (مثل 'احجز موعد' أو 'افتح تذكرة')، رد بـ JSON: {\"action\": \"create_ticket\", \"params\": {...}}. وإلا رد بـ null.";

        $response = $this->groq->chat([['role' => 'system', 'content' => $prompt], ['role' => 'user', 'content' => $userMessage]]);

        // Simple JSON parse attempt
        if (str_starts_with(trim($response['content']), '{')) {
            return $response['content'];
        }

        return null;
    }
}

