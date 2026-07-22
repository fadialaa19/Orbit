<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    // نفس بادئة العنوان المستخدمة في StudentDashboardController لتذاكر طلب
    // استخراج المستندات، تسمح بفصلها عن تذاكر الدعم الفني العادية بدون عمود جديد.
    private const DOCUMENT_PREFIX = '📄 طلب استخراج مستند:';

    private function scopeDocuments($query)
    {
        return $query->where('subject', 'like', self::DOCUMENT_PREFIX . '%');
    }

    private function scopeSupport($query)
    {
        return $query->where(function ($q) {
            $q->where('subject', 'not like', self::DOCUMENT_PREFIX . '%')->orWhereNull('subject');
        });
    }

    public function index(Request $request)
    {
        $activeTab = $request->query('view') === 'documents' ? 'documents' : 'support';
        $activeScope = $activeTab === 'documents' ? 'scopeDocuments' : 'scopeSupport';

        $stats = [
            'pending'   => $this->{$activeScope}(SupportTicket::query())->where('status', 'pending')->count(),
            'resolved'  => $this->{$activeScope}(SupportTicket::query())->where('status', 'resolved')->count(),
            'closed'    => $this->{$activeScope}(SupportTicket::query())->where('status', 'closed')->count(),
            'emergency' => $this->{$activeScope}(SupportTicket::query())->where('priority', 'emergency')->where('status', '!=', 'resolved')->count(),
        ];

        $counts = [
            'support'   => $this->scopeSupport(SupportTicket::query())->count(),
            'documents' => $this->scopeDocuments(SupportTicket::query())->count(),
        ];

        // Check if this is an AJAX request for polling (explicit check for fetch/axios)
        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                 $request->hasHeader('X-Polling-Request') ||
                 $request->query('api') === 'true';

        if ($isAjax) {
            $tickets = $this->{$activeScope}(SupportTicket::with('user'))
                ->latest()
                ->paginate(15);

            return response()->json([
                'tickets' => $tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'subject' => $ticket->subject,
                        'status' => $ticket->status,
                        'user_name' => $ticket->user->name ?? 'مستخدم',
                        'created_at' => $ticket->created_at->format('Y-m-d H:i'),
                    ];
                }),
                'stats' => $stats
            ]);
        }

        // Default: return HTML view
        $tickets = $this->{$activeScope}(SupportTicket::with('user'))
            ->latest()
            ->paginate(15);

        return view('admin.tickets', compact('tickets', 'stats', 'activeTab', 'counts'));
    }

    public function show($id)
{
    try {
        // نستخدم findOrFail لضمان وجود التذكرة
        $ticket = SupportTicket::with(['user', 'messages'])->findOrFail($id);

        $messages = $ticket->messages()->orderBy('created_at', 'asc')->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'message_text' => $msg->message_text,
                'file_path' => $msg->file_path,
                'created_at' => $msg->created_at->format('Y-m-d H:i'),
                // هنا نضع اسم افتراضي إذا كان المستخدم غير موجود (مثل رسائل AI)
                'sender_name' => $msg->sender_type === 'ai' ? 'الأستاذة نور' : ($msg->sender_id ? $msg->sender->name : 'نظام'),
            ];
        });

        return response()->json([
            'id' => $ticket->id,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'ai_summary' => $ticket->ai_summary,
            'messages' => $messages,
        ]);
    } catch (\Exception $e) {
        \Log::error("خطأ في فتح محادثة الأدمن: " . $e->getMessage());
        return response()->json(['error' => 'حدث خطأ في السيرفر'], 500);
    }
}

    public function destroy(SupportTicket $ticket)
    {
        $ticket->messages()->delete();
        $ticket->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.tickets.index')->with('success', 'تم حذف التذكرة.');
    }

    public function rename(Request $request, $id)
    {
        $request->validate(['subject' => 'required|string|max:150']);
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['subject' => $request->subject]);

        return response()->json(['success' => true, 'subject' => $ticket->subject]);
    }

    public function deleteMessage($id, $messageId)
    {
        $ticket = SupportTicket::findOrFail($id);
        $message = $ticket->messages()->where('id', $messageId)->firstOrFail();
        $message->delete();

        return response()->json(['success' => true]);
    }

    public function reply(Request $request, $id)
{
    try {
        // 1. البحث عن التذكرة
        $ticket = \App\Models\SupportTicket::findOrFail($id);

        // 2. التحقق من البيانات (نقبل النص أو الملف)
        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['success' => false, 'error' => 'لا يمكن إرسال رسالة فارغة'], 422);
        }

        // 3. معالجة الملف (إن وجد) مع التأكد من وجود المجلد
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        // 4. إنشاء الرسالة (تأكد من مطابقة أسماء الأعمدة في الجدول)
        $message = \App\Models\Message::create([
            'messageable_id'   => $ticket->id,
            'messageable_type' => get_class($ticket), // ستكون App\Models\SupportTicket
            'sender_id'        => auth()->id(),
            'sender_type'      => 'admin',
            'message_text'     => $request->message ?? '',
            'file_path'        => $filePath,
        ]);

        // 5. البث المباشر (تأكد من تشغيل php artisan reverb:start)
        try {
            broadcast(new \App\Events\ChatMessageSent($message->load('sender')))->toOthers();
        } catch (\Exception $e) {
            // لا تعطل العملية لو فشل البث، فقط سجل الخطأ
            \Log::warning("Broadcast failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id'           => $message->id,
                'sender_type'  => 'admin',
                'message_text' => $message->message_text,
                'created_at'   => $message->created_at->format('H:i'),
                'file_path'    => $message->file_path,
            ]
        ]);

    } catch (\Exception $e) {
        // أهم سطر: سيطبع لك الخطأ الحقيقي في ملف storage/logs/laravel.log
        \Log::error("Critical Admin Chat Error: " . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error'   => 'حدث خطأ داخلي: ' . $e->getMessage()
        ], 500);
    }
}
}

