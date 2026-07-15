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
        Schema::table('scholarships', function (Blueprint $table) {
            // نفس مقياس 0-100 المستخدم لكل حقول المعدل عند الطالب (high_school_gpa،
            // bachelor_gpa، ...) - بيسمح بمقارنة رقمية أكيدة بدل ما الذكاء الاصطناعي
            // يحاول يفهم الرقم من نص الشروط الحر.
            $table->decimal('min_gpa', 5, 2)->nullable()->after('financial_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn('min_gpa');
        });
    }
};
