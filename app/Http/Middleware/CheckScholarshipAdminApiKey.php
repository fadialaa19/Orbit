<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckScholarshipAdminApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $configuredKey = config('services.scholarship_admin_api.key');
        $providedKey = $request->header('X-Admin-Api-Key');

        if (!$configuredKey || !$providedKey || !hash_equals($configuredKey, $providedKey)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
