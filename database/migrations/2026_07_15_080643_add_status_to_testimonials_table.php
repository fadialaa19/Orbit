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
        Schema::table('testimonials', function (Blueprint $table) {
            // Existing rows are all admin-authored and already live, so default to
            // 'approved' - only new student submissions get created as 'pending'.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('is_active');
            $table->text('admin_note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_note']);
        });
    }
};
