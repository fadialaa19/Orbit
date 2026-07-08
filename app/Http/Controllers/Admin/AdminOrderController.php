<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\StudentAlertNotification;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'scholarship'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_revenue' => Order::where('status', 'paid')->sum('amount'),
            'pending_count' => Order::where('status', 'pending')->count(),
            'paid_count' => Order::where('status', 'paid')->count(),
            'failed_count' => Order::where('status', 'failed')->count(),
        ];

        return view('admin.orders', compact('orders', 'stats'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:paid,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        $scholarshipTitle = $order->scholarship->title_ar ?? $order->scholarship->title_en ?? 'المنحة';
        if ($request->status === 'paid') {
            $order->user->notify(new StudentAlertNotification(
                'تم قبول طلب الدفع',
                "تم تأكيد دفعك لمنحة \"{$scholarshipTitle}\" ويمكنك الآن متابعة التقديم.",
                route('dashboard.scholarships.show', $order->scholarship_id)
            ));
        } elseif ($request->status === 'rejected') {
            $order->user->notify(new StudentAlertNotification(
                'تم رفض طلب الدفع',
                "لم يتم قبول بيانات الدفع لمنحة \"{$scholarshipTitle}\". يرجى مراجعة التفاصيل وإعادة المحاولة.",
                route('dashboard.scholarships.show', $order->scholarship_id)
            ));
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
}

