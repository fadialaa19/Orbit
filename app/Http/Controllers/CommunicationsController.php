<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\Message;
use App\Models\Room;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\GroqChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;

class CommunicationsController extends Controller
{
    public function index()
    {
        return view('dashboard.communications');
    }

    public function chats()
    {
        $user = Auth::user();
        $ai_chats = $user->rooms()->where('type', 'ai')->latest()->get();
        $support_chats = $user->rooms()->where('type', 'support')->latest()->get();
        $tickets = $user->supportTickets()->latest()->get();

        return response()->json([
            'ai_chats' => $ai_chats->map(fn($chat) => $this->formatChat($chat)),
            'support_chats' => $support_chats->map(fn($chat) => $this->formatChat($chat)),
            'tickets' => $tickets->map(fn($ticket) => $this->formatTicket($ticket)),
        ]);
    }



    public function getMessages($id, $type)
    {
        $user = Auth::user();
        $messageable = $this->resolveMessageable($id, $type, $user);

        $messages = $messageable->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->reverse()->values(),
        ]);
    }

    public function sendMessage(Request $request, $id, $type)
{
    $request->validate(['message' => 'required|string|max:2000']);
    $user = Auth::user();
    $messageable = $this->resolveMessageable($id, $type, $user);

    // 1. تخزين رسالة المستخدم وبثها
    $userMessage = Message::create([
        'messageable_id' => $messageable->id,
        'messageable_type' => get_class($messageable),
        'sender_id' => $user->id,
        'sender_type' => 'user',
        'message_text' => $request->message,
    ]);
    // فشل البث (مثلاً مشكلة اتصال بـ Reverb) لا يجب أن يُسقط الطلب بالكامل،
    // لأن الرسالة محفوظة أصلاً في قاعدة البيانات.
    try {
        broadcast(new \App\Events\ChatMessageSent($userMessage->load('sender:id,name')))->toOthers();
    } catch (\Exception $e) {
        \Log::warning("ChatMessageSent broadcast failed (sendMessage/user): " . $e->getMessage());
    }

    if ($type === 'ai' || (isset($messageable->type) && $messageable->type === 'ai')) {
        $groq = new \App\Services\GroqChatService();
        
        // جلب سياق المحادثة
        $history = $messageable->messages()->latest()->take(10)->get()
            ->map(fn($m) => [
                'role' => $m->sender_type === 'user' ? 'user' : 'assistant',
                'content' => $m->message_text
            ])->reverse()->values()->toArray();

        $result = $groq->chat($history);

        // 2. إذا تم تفعيل طلب الدعم الفني تلقائياً
        if ($result['trigger_support']) {
            // إنشاء تذكرة دعم فني جديدة لهذا المستخدم
            $ticket = \App\Models\SupportTicket::create([
                'user_id' => $user->id,
                'subject' => 'تذكرة محولة تلقائياً: ' . \Illuminate\Support\Str::limit($request->message, 50),
                'priority' => 'high',
                'status' => 'pending',
                'ai_summary' => 'هذه التذكرة تم إنشاؤها تلقائياً بواسطة الأستاذة نور بناءً على طلب المستخدم.',
            ]);

            // نقل رسالة المستخدم الأخيرة لتكون أول رسالة في التذكرة
            Message::create([
                'messageable_id' => $ticket->id,
                'messageable_type' => get_class($ticket),
                'sender_id' => $user->id,
                'sender_type' => 'user',
                'message_text' => $request->message,
            ]);

            // إرسال إشعار للمشرفين
            \App\Models\User::admins()->get()->each->notify(new \App\Notifications\NewTicketNotification($ticket));
        }

        // 3. تخزين رد الذكاء الاصطناعي (الذي يخبر المستخدم بأنه تم تحويله)
        $responseMessage = Message::create([
            'messageable_id' => $messageable->id,
            'messageable_type' => get_class($messageable),
            'sender_id' => null,
            'sender_type' => 'ai',
            'message_text' => $result['content'],
        ]);

        try {
            broadcast(new \App\Events\ChatMessageSent($responseMessage->load('sender:id,name')));
        } catch (\Exception $e) {
            \Log::warning("ChatMessageSent broadcast failed (sendMessage/ai): " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'messages' => [$userMessage, $responseMessage],
            'redirect_to_support' => $result['trigger_support'] // نرسل إشارة للفرونت إند
        ]);
    }

    return response()->json(['success' => true, 'messages' => [$userMessage]]);
}



    protected function formatChat($room)
    {
        $lastMsg = $room->messages()->latest()->first();
        return [
            'id' => $room->id,
            'type' => 'room',
            'name' => $room->name,
            'last_message' => $lastMsg?->message_text ?? 'لا توجد رسائل بعد',
            'status' => $room->status ?? 'open',
            'avatar' => $room->avatar ?? '💬',
            'updated_at' => $room->updated_at?->format('H:i') ?? '',
        ];
    }

    protected function formatTicket($ticket)
    {
        $lastMsg = $ticket->messages()->latest()->first();
        return [
            'id' => $ticket->id,
            'type' => 'ticket',
            'name' => $ticket->subject ?? 'تذكرة دعم',
            'last_message' => $lastMsg?->message_text ?? 'لا توجد رسائل بعد',
            'status' => $ticket->status ?? 'open',
            'avatar' => '🛠️',
            'updated_at' => $ticket->updated_at?->format('H:i') ?? '',
        ];
    }

    protected function resolveMessageable($id, $type, $user)
    {
        if ($type === 'ticket') {
            $item = SupportTicket::findOrFail($id);
            if ($item->user_id !== $user->id) abort(403);
        } else { // 'room' or 'ai' or 'support'
            $item = Room::findOrFail($id);
            if ($item->user_id !== $user->id) abort(403);
        }
        return $item;
    }
    public function createNewAiChat(Request $request) {
        $user = Auth::user();
        $chat = $user->rooms()->create([
            'name' => 'محادثة ذكاء اصطناعي جديدة #' . rand(100, 999),
            'type' => 'ai',
            'avatar' => '🤖',
            'status' => 'open'
        ]);
        return response()->json(['chat' => $this->formatChat($chat)]);
    }

public function createNewTicket(Request $request) {
        $user = Auth::user();
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => 'تذكرة دعم جديدة #' . rand(100, 999),
            'priority' => 'medium',
            'status' => 'pending',
        ]);
        // Notify admins via database notification
        \App\Models\User::admins()->get()->each->notify(new \App\Notifications\NewTicketNotification($ticket));
        
        // ✅ Real-time: بث حدث إنشاء تذكرة جديدة للأدمن
        try {
            broadcast(new \App\Events\NewTicketCreatedEvent($ticket))->toOthers();
        } catch (\Exception $e) {
            \Log::warning("NewTicketCreatedEvent broadcast failed: " . $e->getMessage());
        }
        
        return response()->json(['chat' => $this->formatTicket($ticket)]);
    }
}


