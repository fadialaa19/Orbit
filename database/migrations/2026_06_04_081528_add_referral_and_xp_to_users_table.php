<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // نتحقق أولاً؛ إذا لم يكن حقل xp موجوداً نقوم بإضافته
        if (!Schema::hasColumn('users', 'xp')) {
            $table->integer('xp')->default(0);
        }
        
        // نتحقق إذا لم يكن حقل referred_by موجوداً نقوم بإضافته
        if (!Schema::hasColumn('users', 'referred_by')) {
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
