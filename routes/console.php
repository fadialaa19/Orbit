<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Scholarship;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    // تحديث حالة أي منحة نشطة وتجاوزت تاريخ الانتهاء ليصبح 'closed'
    $updatedCount = Scholarship::where('status', 'active')
        ->whereNotNull('deadline')
        ->where('deadline', '<', now()->startOfDay())
        ->update(['status' => 'closed']);
        
    \Log::info("تم تحديث حالة المنح المنتهية تلقائياً. العدد المحدث: {$updatedCount}");
})->daily();