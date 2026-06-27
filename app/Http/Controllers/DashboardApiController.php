<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    /**
     * GET /dashboard/my-tickets/api
     * JSON Mock with pagination.
     */
    public function myTickets(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

        // Mock dataset (keep fields aligned with resources/views/dashboard/tickets.blade.php)
        // tickets.blade.php expects: id, subject, status, created_at
        $all = [];
        $statuses = ['pending', 'resolved', 'closed', 'pending', 'resolved'];
        $subjects = [
            'مشكلة في التسجيل',
            'لا أستطيع رفع المستندات',
            'استفسار عن المنحة',
            'طلب تعديل البيانات',
            'مشكلة في الدفع',
            'تأخير في الرد',
            'خطأ في لوحة التحكم',
            'استفسار حول القبول',
            'مشكلة في حسابي',
            'تحديث حالة الطلب',
        ];

        $total = 42;
        for ($i = 1; $i <= $total; $i++) {
            $status = $statuses[$i % count($statuses)];
            $subject = $subjects[$i % count($subjects)];

            // created_at should be string for UI.
            // We'll return YYYY-MM-DD (you can change formatting later).
            $createdAt = now()->subDays($total - $i)->format('Y-m-d');

            $all[] = [
                'id' => $i,
                'subject' => $subject . ' #' . $i,
                'status' => $status,
                'created_at' => $createdAt,
            ];
        }

        $offset = ($page - 1) * $perPage;
        $slice = array_slice($all, $offset, $perPage);

        // Mock stats (used by tickets.blade.php)
        $stats = [
            'pending' => 0,
            'resolved' => 0,
            'closed' => 0,
            'emergency' => 0,
        ];
        foreach ($all as $t) {
            if (($t['status'] ?? '') === 'pending') $stats['pending']++;
            if (($t['status'] ?? '') === 'resolved') $stats['resolved']++;
            if (($t['status'] ?? '') === 'closed') $stats['closed']++;
        }
        $stats['emergency'] = (int) floor($stats['pending'] / 3);

        return response()->json([
            'tickets' => array_values($slice),
            'stats' => $stats,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
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
        $page = max((int) $request->query('page', 1), 1);
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

        $all = [];
        $titles = [
            'منحة جديدة متاحة',
            'تم قبول طلبك',
            'تذكير بموعد مهم',
            'رسالة من فريق الدعم',
            'تم تحديث حالة الطلب',
            'معلومة تساعدك',
        ];
        $texts = [
            'تم تحديث حالتك بنجاح، راجع التفاصيل في لوحة التحكم.',
            'لدينا اقتراحات لتحسين ملفك، افتح الصفحة لمعرفة المزيد.',
            'يرجى الانتباه لآخر موعد للخطوة القادمة في عملية التقديم.',
            'تمت معالجة طلبك، وقد وصلنا للتو بتحديث جديد.',
            'تم إرسال رسالة جديدة حول مستنداتك المطلوبة.',
        ];
        $hours = [1, 2, 3, 6, 10, 14, 20, 30, 45, 60];

        $total = 26;
        for ($i = 1; $i <= $total; $i++) {
            $isRead = ($i % 4 === 0); // mock

            $all[] = [
                'id' => $i,
                'title' => $titles[$i % count($titles)] . ' #' . $i,
                'text' => $texts[$i % count($texts)],
                'is_read' => !$isRead,
                'read_at' => $isRead ? now()->subHours($hours[$i % count($hours)])->toISOString() : null,
                'time' => ($i % 5 === 0)
                    ? now()->subHours($hours[$i % count($hours)])->diffForHumans()
                    : now()->subMinutes($i * 7)->diffForHumans(),
                // keep link to dashboard pages (same style as current blade)
                'url' => match ($i % 5) {
                    0 => route('dashboard.show'),
                    1 => route('dashboard.scholarships'),
                    2 => route('dashboard.profile'),
                    default => '#',
                },
            ];
        }

        $offset = ($page - 1) * $perPage;
        $slice = array_slice($all, $offset, $perPage);

        return response()->json([
            'notifications' => array_values($slice),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}

