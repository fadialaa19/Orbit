<?php

namespace App\Http\Controllers;

use App\Events\CommunityMessageSent;
use App\Models\Community;
use App\Models\CommunityMute;
use App\Models\Message;
use App\Services\ContentModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommunityController extends Controller
{
    public function index()
    {
        $communities = Community::active()->orderBy('name')->get()->map(function ($community) {
            $lastMessage = $community->messages()->where('is_removed', false)->latest()->first();
            return [
                'id' => $community->id,
                'name' => $community->name,
                'description' => $community->description,
                'type' => $community->type,
                'icon' => $community->icon ?: ($community->type === 'announcement' ? '📢' : '💬'),
                'image' => $community->image,
                'last_message' => $lastMessage?->message_text,
            ];
        });

        return response()->json(['communities' => $communities]);
    }

    public function messages(Community $community)
    {
        if (!$community->is_active) {
            abort(404);
        }

        $messages = $community->messages()
            ->with(['sender:id,name,avatar', 'replyTo.sender:id,name'])
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => $this->formatMessage($m, $community));

        $mute = $community->activeMuteFor(Auth::id());

        return response()->json([
            'community' => [
                'id' => $community->id,
                'name' => $community->name,
                'description' => $community->description,
                'type' => $community->type,
                'icon' => $community->icon ?: ($community->type === 'announcement' ? '📢' : '💬'),
                'image' => $community->image,
            ],
            'messages' => $messages,
            'pinned_message' => $community->pinnedMessage ? $this->formatMessage($community->pinnedMessage->load(['sender:id,name,avatar', 'replyTo.sender:id,name']), $community) : null,
            'muted_until' => $mute?->muted_until?->toISOString(),
            'is_admin' => Auth::user()->isAdmin(),
            'can_post' => $this->canPost($community),
        ]);
    }

    public function send(Request $request, Community $community)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'reply_to_message_id' => 'nullable|integer|exists:chat_messages,id',
        ]);

        if (!$community->is_active) {
            return response()->json(['success' => false, 'message' => 'هذا المجتمع غير متاح حالياً'], 404);
        }

        if (!$this->canPost($community)) {
            return response()->json(['success' => false, 'message' => 'مجتمع التعليمات مخصص لمنشورات الإدارة فقط'], 403);
        }

        $mute = $community->activeMuteFor(Auth::id());
        if ($mute) {
            return response()->json([
                'success' => false,
                'message' => 'تم تقييد إرسال الرسائل عنك مؤقتاً في هذا المجتمع حتى ' . $mute->muted_until->format('Y-m-d H:i'),
            ], 403);
        }

        $moderation = app(ContentModerationService::class);
        if ($moderation->containsProfanity($request->message)) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى الحفاظ على الاحترام في تعاملك مع باقي الأعضاء. لم يتم إرسال رسالتك.',
            ], 422);
        }

        $replyToId = null;
        if ($request->filled('reply_to_message_id')) {
            $replyTarget = Message::where('id', $request->reply_to_message_id)
                ->where('messageable_id', $community->id)
                ->where('messageable_type', Community::class)
                ->first();
            $replyToId = $replyTarget?->id;
        }

        $message = Message::create([
            'messageable_id' => $community->id,
            'messageable_type' => Community::class,
            'sender_id' => Auth::id(),
            'sender_type' => Auth::user()->isAdmin() ? 'admin' : 'user',
            'reply_to_message_id' => $replyToId,
            'message_text' => $request->message,
        ]);

        try {
            broadcast(new CommunityMessageSent($message->load(['sender:id,name,avatar', 'replyTo.sender:id,name']), $community->id))->toOthers();
        } catch (\Exception $e) {
            Log::warning("CommunityMessageSent broadcast failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $this->formatMessage($message, $community),
        ]);
    }

    public function deleteMessage(Community $community, Message $message)
    {
        $this->authorizeAdmin();

        if ((int) $message->messageable_id !== $community->id || $message->messageable_type !== Community::class) {
            abort(404);
        }

        $message->update([
            'is_removed' => true,
            'removed_by' => Auth::id(),
            'removed_at' => now(),
        ]);

        if ($community->pinned_message_id === $message->id) {
            $community->update(['pinned_message_id' => null]);
        }

        return response()->json(['success' => true]);
    }

    public function pinMessage(Community $community, Message $message)
    {
        $this->authorizeAdmin();

        if ((int) $message->messageable_id !== $community->id || $message->messageable_type !== Community::class) {
            abort(404);
        }

        $community->update(['pinned_message_id' => $message->id]);

        return response()->json(['success' => true]);
    }

    public function unpinMessage(Community $community)
    {
        $this->authorizeAdmin();

        $community->update(['pinned_message_id' => null]);

        return response()->json(['success' => true]);
    }

    public function muteMember(Request $request, Community $community)
    {
        $this->authorizeAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'duration' => 'required|in:1h,1d,3d,7d,permanent',
            'reason' => 'nullable|string|max:255',
        ]);

        $durations = [
            '1h' => now()->addHour(),
            '1d' => now()->addDay(),
            '3d' => now()->addDays(3),
            '7d' => now()->addDays(7),
            'permanent' => now()->addYears(100),
        ];

        CommunityMute::create([
            'community_id' => $community->id,
            'user_id' => $request->user_id,
            'muted_until' => $durations[$request->duration],
            'muted_by' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return response()->json(['success' => true]);
    }

    public function unmuteMember(Request $request, Community $community)
    {
        $this->authorizeAdmin();

        $request->validate(['user_id' => 'required|exists:users,id']);

        $community->mutes()
            ->where('user_id', $request->user_id)
            ->where('muted_until', '>', now())
            ->update(['muted_until' => now()]);

        return response()->json(['success' => true]);
    }

    protected function canPost(Community $community): bool
    {
        return $community->type !== 'announcement' || Auth::user()->isAdmin();
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);
    }

    protected function formatMessage(Message $message, Community $community): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->name ?? 'مستخدم',
            'sender_type' => $message->sender_type,
            'sender_avatar' => $message->sender?->avatar,
            'message_text' => $message->is_removed ? null : $message->message_text,
            'is_removed' => (bool) $message->is_removed,
            'is_pinned' => $community->pinned_message_id === $message->id,
            'reply_to' => ($message->replyTo && !$message->replyTo->is_removed) ? [
                'id' => $message->replyTo->id,
                'sender_name' => $message->replyTo->sender?->name ?? 'مستخدم',
                'message_text' => $message->replyTo->message_text,
            ] : null,
            'created_at' => $message->created_at->format('H:i'),
            'created_at_full' => $message->created_at->toISOString(),
        ];
    }
}
