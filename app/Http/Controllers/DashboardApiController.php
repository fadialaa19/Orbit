<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    /**
     * GET /dashboard/my-tickets/api
     */
    public function myTickets(Request $request)
    {
        $user = Auth::user();
        $page = max((int) $request->query('page', 1), 1);
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

        $query = $user->supportTickets()->latest();

        $total = (clone $query)->count();
        $tickets = $query->forPage($page, $perPage)->get()->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at->format('Y-m-d'),
            ];
        });

        $stats = [
            'pending' => $user->supportTickets()->where('status', 'pending')->count(),
            'resolved' => $user->supportTickets()->where('status', 'resolved')->count(),
            'closed' => $user->supportTickets()->where('status', 'closed')->count(),
            'emergency' => $user->supportTickets()->where('status', 'pending')->where('priority', 'emergency')->count(),
        ];

        return response()->json([
            'tickets' => $tickets->values(),
            'stats' => $stats,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / max($perPage, 1)),
            ],
        ]);
    }

    /**
     * GET /dashboard/my-favorites/api
     */
    public function myFavorites(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['favorites' => []], 200);
        }

        // scholarships favorites relation is defined on User model
        $favorites = $user->favoriteScholarships()
            ->get(['scholarships.id', 'scholarships.title_ar', 'scholarships.title_en', 'scholarships.country', 'scholarships.university', 'scholarships.category', 'scholarships.coverage', 'scholarships.price', 'scholarships.financial_value', 'scholarships.applicants_count', 'scholarships.status']);

        $items = $favorites->map(function ($s) {
            $title = $s->title_ar ?: $s->title_en;
            $category = $s->category ?: $s->country ?: 'منحة';

            return [
                // UI uses f.id as key
                'id' => $s->id,
                // optional fields used by the Blade
                'title' => $title,
                'category' => $category,
                // keep compatibility with existing frontend placeholders
                'match' => isset($s->coverage) && is_array($s->coverage)
                    ? ($s->coverage['match'] ?? '85%')
                    : '85%',
                'amount' => $s->price ? ('$' . number_format((float) $s->price, 0)) : '$50,000',
                'funding' => !empty($s->financial_value) ? (string) $s->financial_value : 'ممولة كاملة',
            ];
        })->values();

        return response()->json([
            'favorites' => $items,
        ]);
    }

    /**
     * GET /dashboard/my-notifications/api
     */
    public function myNotifications(Request $request)
    {
        $user = Auth::user();
        $page = max((int) $request->query('page', 1), 1);
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

        $query = $user->notifications()->latest();
        $total = (clone $query)->count();

        $notifications = $query->forPage($page, $perPage)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'إشعار',
                'text' => $n->data['body'] ?? '',
                'is_read' => !is_null($n->read_at),
                'read_at' => $n->read_at?->toISOString(),
                'time' => $n->created_at->diffForHumans(),
                'url' => $n->data['link'] ?? '#',
            ];
        });

        return response()->json([
            'notifications' => $notifications->values(),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / max($perPage, 1)),
            ],
        ]);
    }

    /**
     * POST /dashboard/notifications/read-all
     */
    public function markAllNotificationsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}

