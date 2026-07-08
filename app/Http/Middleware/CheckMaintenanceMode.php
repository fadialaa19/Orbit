<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // 1. جلب البيانات أولاً قبل استخدامها
        $maintenanceMode = Setting::get('maintenance_mode', false);
        $until = Setting::get('maintenance_until');
        $message = Setting::get('maintenance_message', 'الموقع قيد الصيانة حالياً، سنعود قريباً.');

        // 2. التحقق مما إذا كان الوقت قد انتهى لفتح الموقع تلقائياً
        if ($maintenanceMode && $until && now()->greaterThan($until)) {
            $setting = Setting::first();
            if ($setting) {
                $setting->update([
                    'maintenance_mode' => false,
                    'maintenance_until' => null
                ]);
                $maintenanceMode = false; // تحديث المتغير المحلي لفتح الموقع فوراً
                \Illuminate\Support\Facades\Cache::flush(); // مسح الكاش
            }
        }

        // 3. إذا كان وضع الصيانة مفعلاً (ولم ينتهِ وقته بعد)
        if ($maintenanceMode) {
            // السماح للأدمن بالدخول (تعديل حسب نظام الأدمن عندك)
            if (Auth::check() && Auth::user()->isAdmin()) {
                return $next($request);
            }

            // استثناء صفحات الإدارة واللوجن لضمان قدرتك على الدخول
            if ($request->is('admin*') || $request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // عرض صفحة الصيانة
            return response()->view('errors.maintenance', compact('message'), 503);
        }

        return $next($request);
    }
}