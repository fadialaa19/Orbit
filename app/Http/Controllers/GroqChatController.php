<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\SupportTicket;
use App\Notifications\NewTicketNotification;
use App\Services\GroqChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class GroqChatController extends Controller
{
    private GroqChatService $groq;

    public function __construct(GroqChatService $groq)
    {
        $this->groq = $groq;
    }

    public function chat(Request $request)
    {
        $key = 'groq-chat:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'reply' => '⏳ لقد تجاوزت الحد المسموح به من الرسائل. يرجى الانتظار دقيقة.',
                'force_close' => false,
                'trigger_support' => false,
                'support_ticket_id' => null,
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        if (session('groq_chat_banned', false)) {
            return response()->json([
                'success' => true,
                'reply' => '⛔ تم إغلاق هذه المحادثة نهائياً بسبب استخدامك لألفاظ غير لائقة.',
                'force_close' => true,
                'trigger_support' => false,
                'support_ticket_id' => null,
            ]);
        }

        $userMessage = $request->input('message');
        $history = session('groq_chat_history', []);
        $history[] = ['role' => 'user', 'content' => $userMessage];
        $history = array_slice($history, -20);

        $result = $this->groq->chat($history);

        if ($result['force_close']) {
            session(['groq_chat_banned' => true]);
        }

        $supportTicketId = null;
        if ($result['trigger_support']) {
            if (Auth::check()) {
                $supportTicketId = $this->createSupportTicket($userMessage, $history);
            }
            session()->forget('groq_chat_history');
        } else {
            $history[] = ['role' => 'assistant', 'content' => $result['content']];
            session(['groq_chat_history' => $history]);
        }

        return response()->json([
            'success' => true,
            'reply' => $result['content'],
            'force_close' => $result['force_close'],
            'trigger_support' => $result['trigger_support'],
            'support_ticket_id' => $supportTicketId,
            'is_authenticated' => Auth::check(),
        ]);
    }

    public function clearHistory(Request $request)
    {
        session()->forget('groq_chat_history');
        return response()->json(['success' => true]);
    }

    public function clearBan(Request $request)
    {
        session()->forget('groq_chat_banned');
        return response()->json(['success' => true]);
    }

    private function createSupportTicket(string $lastMessage, array $history): int
    {
        $user = Auth::user();

        $subject = 'طلب دعم فني من الشات: ' . mb_substr($lastMessage, 0, 60);
        if (mb_strlen($lastMessage) > 60) {
            $subject .= '...';
        }

        $lines = [];
        foreach ($history as $msg) {
            $prefix = ($msg['role'] === 'user') ? 'المستخدم: ' : 'AI: ';
            $lines[] = $prefix . $msg['content'];
        }
        $chatHistoryText = implode("\n---\n", $lines);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $subject,
            'priority' => 'high',
            'status' => 'pending',
            'ai_summary' => 'تم إنشاء هذا الطلب تلقائياً عند طلب المستخدم التواصل مع الدعم الفني عبر الشات الذكي.' . "\n\n" . 'آخر رسالة: ' . $lastMessage,
            'chat_history' => $history,
            'last_reply_at' => now(),
        ]);

        Message::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_type' => 'user',
            'message_text' => $lastMessage . "\n\n" . '--- سياق المحادثة الكامل ---' . "\n" . $chatHistoryText,
        ]);

        try {
            $admins = \App\Models\User::whereIn('role', ['super_admin', 'support_admin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewTicketNotification($ticket));
            }
        } catch (\Exception $e) {
            // Silently fail notification
        }

        return $ticket->id;
    }
}

