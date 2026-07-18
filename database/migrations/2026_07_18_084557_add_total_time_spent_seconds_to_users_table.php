<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // عدّاد تراكمي دائم (لا يُصفَّر أبداً) لعرض إجمالي الوقت المقضي
            // بالموقع - منفصل عن xp_active_seconds اللي بيصفّر جزئياً كل ساعة
            $table->unsignedBigInteger('total_time_spent_seconds')->default(0)->after('xp_last_heartbeat_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_time_spent_seconds');
        });
    }
};
