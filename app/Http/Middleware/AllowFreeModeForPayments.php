<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AllowFreeModeForPayments
{
    public function handle(Request $request, Closure $next): Response
    {
        // FREE_MODE comes from config/app.php => 'free_mode' => env('FREE_MODE', false)
        if (!config('app.free_mode')) {
            return $next($request);
        }

        // في كل الأحوال، لا نفترض وجود Auth middleware هنا؛ نؤكد على المستخدم.
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /**
         * Route param name in routes/web.php is {scholarship}
         * The controller type-hints Scholarship model, so here it should be either
         * an instance or an id.
         */
        $scholarship = $request->route('scholarship');
        $scholarshipId = is_object($scholarship) ? $scholarship->id : (int) $scholarship;

        if (!$scholarshipId) {
            // إذا لم نستطع تحديد المنحة نترك الطلب.
            return $next($request);
        }

        $hasPaid = Order::paid()
            ->where('user_id', Auth::id())
            ->where('scholarship_id', $scholarshipId)
            ->exists();

        if (!$hasPaid) {
            // ننشئ طلباً مدفوعاً تلقائياً حتى يُسمح بالوصول.
            // نضع minimal fields فقط لأن الـ app/Models/Order يسمح بـ mass assignment لها.
            Order::create([
                'user_id' => Auth::id(),
                'scholarship_id' => $scholarshipId,
                'amount' => 0,
                'status' => 'paid',
                'payment_method' => 'free_mode',
                'transaction_id' => 'free_mode',
                'bank_name' => null,
                'transfer_from' => null,
                'receipt_image' => null,
                'admin_notes' => 'تم تفعيل الوصول المجاني تلقائياً بسبب FREE_MODE',
            ]);
        }

        return redirect()
            ->route('dashboard.scholarships.show', $scholarshipId)
            ->with('success', 'تم تفعيل المنحة مجاناً بنجاح');
    }
}

