<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Message;
use App\Notifications\StudentAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * ChatController handles admin-to-student ticket replies and student replies.
 */
class ChatController extends Controller
{
    /**
     * Admin reply to a support ticket.
     */
    public function adminReply(Request $request, $id)
{
    try {
        $ticket = \App\Models\SupportTicket::findOrFail($id);

        $request->validate([
            'message' => 'required|string',
        ]);

        // الخطأ كان هنا: يجب إرسال قيم الـ Polymorphic
        $message = \App\Models\Message::create([
            'messageable_id'   => $ticket->id,
            'messageable_type' => get_class($ticket), // هذا سيخزن App\Models\SupportTicket
            'sender_id'        => auth()->id(),
            'sender_type'      => 'admin',
            'message_text'     => $request->message,
            'file_path'        => $request->hasFile('file') ? $request->file('file')->store('chat_files', 'public') : null,
        ]);

        // بث الحدث (Real-time)
        // فشل البث لا يجب أن يُسقط الطلب بالكامل، لأن الرسالة محفوظة أصلاً.
        try {
            broadcast(new \App\Events\ChatMessageSent($message->load('sender')))->toOthers();
        } catch (\Exception $e) {
            \Log::warning("ChatMessageSent broadcast failed (adminReply): " . $e->getMessage());
        }

        $ticket->user->notify(new StudentAlertNotification(
            'رد جديد من الدعم الفني',
            Str::limit($request->message, 100),
            route('dashboard.tickets.show', $ticket->id)
        ));

        return response()->json([
            'success' => true,
            'message' => [
                'id'           => $message->id,
                'sender_type'  => 'admin',
                'message_text' => $message->message_text,
                'created_at'   => $message->created_at->format('H:i'),
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error("ChatController Error: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
/**
     * Student reply to their own support ticket.
     */
    public function studentReply(Request $request, SupportTicket $ticket)
    {
        if (!Auth::check() || Auth::id() !== $ticket->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($ticket->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'التذكرة مغلقة. استخدم الذكاء الاصطناعي.'], 400);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('chat-files/users/' . Auth::id(), $filename, 'public');
        }

        // ✅ استخدام نفس هيكل الـ Polymorphic زي الأدمن
        $message = Message::create([
            'messageable_id'   => $ticket->id,
            'messageable_type' => get_class($ticket),
            'sender_id'        => Auth::id(),
            'sender_type'      => 'user',
            'message_text'     => $request->message,
            'file_path'       => $filePath,
        ]);

        $ticket->update([
            'last_reply_at' => now(),
            'status' => 'pending',
        ]);

        // ✅ بث الحدث للطالب نفسه وللأدمن (Real-time)
        // لا نسمح لفشل البث (مثلاً مشكلة اتصال بـ Reverb) بإسقاط الطلب بالكامل،
        // لأن الرسالة أصلاً محفوظة في قاعدة البيانات بهذه المرحلة.
        try {
            broadcast(new \App\Events\ChatMessageSent($message->load('sender')))->toOthers();
        } catch (\Exception $e) {
            \Log::warning("ChatMessageSent broadcast failed (studentReply): " . $e->getMessage());
        }

        // admin.tickets.show بيرجع JSON خام (مخصص لـ AJAX)، مش صفحة حقيقية - نربط
        // لصفحة القائمة نفسها مع فتح التذكرة تلقائياً وتحديد التبويب الصحيح.
        $isDocumentTicket = str_starts_with($ticket->subject ?? '', '📄 طلب استخراج مستند:');
        \App\Models\User::admins()->get()->each->notify(new StudentAlertNotification(
            'رد جديد على تذكرة #' . $ticket->id,
            $ticket->subject . ': ' . Str::limit($request->message, 100),
            route('admin.tickets.index', ['view' => $isDocumentTicket ? 'documents' : 'support', 'open' => $ticket->id])
        ));

        return response()->json([
            'success' => true,
            'message' => [
                'id'           => $message->id,
                'sender_type'  => 'user',
                'message_text' => $message->message_text,
                'created_at'   => $message->created_at->format('H:i'),
            ]
        ]);
    }

    /**
     * Get all messages for a ticket (for both admin and student).
     */
    public function ticketMessages(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();

        // Allow if admin OR if student owns the ticket
        if (!$user || (!$user->isAdmin() && $user->id !== $ticket->user_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $messages = $ticket->messages()->with('sender:id,name')->orderBy('created_at')->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'message_text' => $msg->message_text,
                'file_path' => $msg->file_path,
                'created_at' => $msg->created_at->format('Y-m-d H:i'),
                'user' => $msg->sender ? ['name' => $msg->sender->name] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'ticket' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'ai_summary' => $ticket->ai_summary,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Admin can mark a ticket as resolved.
     */
    public function resolveTicket(Request $request, SupportTicket $ticket)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $ticket->update(['status' => 'resolved']);

        $ticket->user->notify(new StudentAlertNotification(
            'تم حل تذكرتك',
            "تم حل تذكرة الدعم \"{$ticket->subject}\" من قبل فريق الدعم الفني.",
            route('dashboard.tickets.show', $ticket->id)
        ));

        return response()->json(['success' => true]);
    }

    /**
     * Admin can close a ticket.
     */
    public function closeTicket(Request $request, SupportTicket $ticket)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $ticket->update(['status' => 'closed']);

        $ticket->user->notify(new StudentAlertNotification(
            'تم إغلاق تذكرتك',
            "تم إغلاق تذكرة الدعم \"{$ticket->subject}\".",
            route('dashboard.tickets.show', $ticket->id)
        ));

        return response()->json(['success' => true]);
    }

/**
     * AI reply for closed tickets (student only).
     */
    public function aiReply(Request $request, SupportTicket $ticket)
    {
        if (!Auth::check() || Auth::id() !== $ticket->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($ticket->status !== 'closed') {
            return response()->json(['success' => false, 'message' => 'التذكرة مفتوحة. استخدم الرد العادي.'], 400);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Use Groq service
        $groqService = new \App\Services\GroqChatService();
        
        // Build history for context: last 5 messages + ticket subject
        $recentMessages = $ticket->messages()->latest('created_at')->limit(5)->get()->reverse()->toArray();
        $history = [];
        foreach ($recentMessages as $msg) {
            $role = $msg['sender_type'] === 'user' ? 'user' : 'assistant';
            $history[] = ['role' => $role, 'content' => $msg['message_text']];
        }
        $history[] = ['role' => 'user', 'content' => $request->message];

        $result = $groqService->chat($history);
        $aiResponse = $result['content'];

        // ✅ Fixed: Use polymorphic schema like adminReply and studentReply
        $message = Message::create([
            'messageable_id'   => $ticket->id,
            'messageable_type' => get_class($ticket),
            'sender_id'        => Auth::id(),
            'sender_type'      => 'ai',
            'message_text'     => $aiResponse,
            'file_path'        => null,
        ]);

        // ✅ Broadcast AI message for real-time updates
        try {
            broadcast(new \App\Events\ChatMessageSent($message->load('sender')))->toOthers();
        } catch (\Exception $e) {
            \Log::warning("ChatMessageSent broadcast failed (aiReply): " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_type' => 'ai',
                'message_text' => $aiResponse,
                'created_at' => $message->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }
}


