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
            // بيصير true لما نمنح الداعي نقاطه بعد ما هاد الطالب (المدعو) يكمل
            // نسبة 50% من ملفه الشخصي - يمنع تكرار المنح ويمنع حسابات وهمية
            // فاضية من كسب نقاط فورية لمجرد التسجيل.
            $table->boolean('referral_reward_granted')->default(false)->after('referred_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_reward_granted');
        });
    }
};
