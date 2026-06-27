<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
}

