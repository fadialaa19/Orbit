<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * نقطة دخول موحّدة لكل استدعاءات الذكاء الاصطناعي بالموقع (الشات بوت، حساب
 * التوافق، توليد محتوى المنح). تجرب Groq أولاً، وإذا فشل (خصوصاً بسبب تجاوز
 * الحصة اليومية rate_limit_exceeded) بترجع تلقائياً لـ Gemini كبديل مجاني،
 * بدل ما تفشل الميزة بالكامل لما تخلص حصة مزوّد واحد.
 */
class AiCompletionService
{
    private ?string $groqKey;
    private string $groqModel;
    private string $groqUrl = 'https://api.groq.com/openai/v1/chat/completions';

    private ?string $geminiKey;
    private string $geminiModel;

    public function __construct()
    {
        $this->groqKey = config('services.groq.key');
        $this->groqModel = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->geminiKey = config('services.gemini.key');
        $this->geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * @param array $messages مصفوفة رسائل بصيغة OpenAI المعتادة: [['role'=>'system'|'user'|'assistant','content'=>string], ...]
     * @param array $options temperature, max_tokens, timeout, json_mode (bool)
     * @return array{success: bool, content: string, provider: ?string, error: ?string}
     */
    public function complete(array $messages, array $options = []): array
    {
        $groqResult = $this->tryGroq($messages, $options);
        if ($groqResult['success']) {
            return $groqResult;
        }

        Log::warning('AiCompletionService: Groq failed, falling back to Gemini. Reason: ' . $groqResult['error']);

        $geminiResult = $this->tryGemini($messages, $options);
        if ($geminiResult['success']) {
            return $geminiResult;
        }

        Log::error('AiCompletionService: Gemini fallback also failed. Reason: ' . $geminiResult['error']);

        return [
            'success' => false,
            'content' => '',
            'provider' => null,
            'error' => $groqResult['error'] ?? $geminiResult['error'] ?? 'فشل كل مزودي الذكاء الاصطناعي.',
        ];
    }

    private function tryGroq(array $messages, array $options): array
    {
        if (empty($this->groqKey)) {
            return $this->fail('مفتاح Groq API غير مضبوط.');
        }

        try {
            $payload = [
                'model' => $this->groqModel,
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.5,
                'max_tokens' => $options['max_tokens'] ?? 2048,
            ];
            if (!empty($options['json_mode'])) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->groqKey,
                'Content-Type' => 'application/json',
            ])->timeout($options['timeout'] ?? 30)->post($this->groqUrl, $payload);

            if (!$response->successful()) {
                return $this->fail('Groq: ' . $response->body());
            }

            return [
                'success' => true,
                'content' => $response->json('choices.0.message.content', ''),
                'provider' => 'groq',
                'error' => null,
            ];
        } catch (\Exception $e) {
            return $this->fail('Groq exception: ' . $e->getMessage());
        }
    }

    private function tryGemini(array $messages, array $options): array
    {
        if (empty($this->geminiKey)) {
            return $this->fail('مفتاح Gemini API غير مضبوط.');
        }

        try {
            $systemInstruction = null;
            $contents = [];

            foreach ($messages as $message) {
                if ($message['role'] === 'system') {
                    $systemInstruction = $systemInstruction
                        ? $systemInstruction . "\n\n" . $message['content']
                        : $message['content'];
                    continue;
                }

                $contents[] = [
                    'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [['text' => $message['content']]],
                ];
            }

            $generationConfig = [
                'temperature' => $options['temperature'] ?? 0.5,
                'maxOutputTokens' => $options['max_tokens'] ?? 2048,
            ];
            if (!empty($options['json_mode'])) {
                $generationConfig['responseMimeType'] = 'application/json';
            }

            $payload = ['contents' => $contents, 'generationConfig' => $generationConfig];
            if ($systemInstruction) {
                $payload['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout($options['timeout'] ?? 30)
                ->post($url . '?key=' . $this->geminiKey, $payload);

            if (!$response->successful()) {
                return $this->fail('Gemini: ' . $response->body());
            }

            $content = $response->json('candidates.0.content.parts.0.text', '');

            if ($content === '') {
                $finishReason = $response->json('candidates.0.finishReason');
                return $this->fail('Gemini returned empty content (finishReason: ' . ($finishReason ?? 'unknown') . ')');
            }

            return [
                'success' => true,
                'content' => $content,
                'provider' => 'gemini',
                'error' => null,
            ];
        } catch (\Exception $e) {
            return $this->fail('Gemini exception: ' . $e->getMessage());
        }
    }

    private function fail(string $error): array
    {
        return ['success' => false, 'content' => '', 'provider' => null, 'error' => $error];
    }
}
