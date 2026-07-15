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
        Schema::create('scholarship_match_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('summary')->nullable();
            $table->json('matched_criteria')->nullable();
            $table->json('gaps')->nullable();
            // نعيد الحساب لو بروفايل الطالب أو شروط المنحة اتغيّرت بعد آخر تحليل،
            // بدل ما نعتمد بس على قِدَم التاريخ.
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->unique(['user_id', 'scholarship_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_match_scores');
    }
};
