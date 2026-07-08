<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
{
    // 1. الإحصائيات السريعة
    $stats = [
        'total_students'      => \App\Models\User::where('role', 'student')->count(),
        
        // التحقق من وجود الموديلات قبل الطلب لتجنب الـ Error
        'active_scholarships' => class_exists('\App\Models\Scholarship') 
                                 ? \App\Models\Scholarship::where('status', 'active')->count() 
                                 : 0,
                                 
        'total_revenue'       => class_exists('\App\Models\Order') 
                                 ? \App\Models\Order::where('status', 'paid')->sum('amount') / 1000 
                                 : 0, // إذا لم يوجد جدول طلبات سيظهر 0K
                                 
        'pending_tickets'     => class_exists('\App\Models\SupportTicket') 
                                 ? \App\Models\SupportTicket::where('status', 'pending')->count() 
                                 : 0,
    ];

    // 2. أحدث طلبات الالتحاق
    $recent_applications = ScholarshipApplication::with(['user', 'scholarship'])
        ->latest()
        ->take(6)
        ->get();

    return view('admin.index', compact('stats', 'recent_applications'));
}

    public function getLatestNotifications()
{
    // نجلب آخر 5 إشعارات للمدير المسجل دخوله
    $notifications = auth()->user()->notifications()->take(5)->get()->map(function($n) {
        return [
            'id' => $n->id,
            'data' => $n->data, // تحتوي على title, message, link
            'read_at' => $n->read_at,
            'created_at_human' => $n->created_at->diffForHumans(),
        ];
    });

    return response()->json($notifications);
}

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);

        return view('admin.notifications', compact('notifications'));
    }
}

