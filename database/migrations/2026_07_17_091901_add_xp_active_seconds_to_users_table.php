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
            // عدّاد ثوانٍ متراكم منذ آخر دفعة XP لقضاء الوقت بالموقع (يُصفَّر جزئياً كل ما يتخطى الساعة)
            $table->unsignedInteger('xp_active_seconds')->default(0)->after('xp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('xp_active_seconds');
        });
    }
};
