<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = auth()->user();

        // 1. التأكد أن المستخدم مسجل دخول ومثبت كآدمن
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. إذا كان مدير عام (super_admin) يمر تلقائياً بدون فحص
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // 3. فحص إذا كان يمتلك أياً من الصلاحيات المطلوبة داخل المصفوفة (قد يُمرَّر أكثر
        // من مفتاح صلاحية واحد، مثلاً "communities,support"، حتى تبقى الصلاحية القديمة
        // الأشمل "support" فاتحة الوصول لمن يملكها، مع إتاحة تخصيص أدق لاحقاً)
        if ($user->permissions && array_intersect($permissions, $user->permissions)) {
            return $next($request);
        }

        // إذا لم يكن يمتلك الصلاحية، امنعه فوراً
        abort(403, 'عذراً، لا تمتلك صلاحية للوصول إلى هذه الصفحة.');
    }
}