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
        Schema::table('chat_messages', function (Blueprint $table) {
            // رسالة محذوفة من الإدارة بتترك أثر شفاف بدل ما تختفي بصمت -
            // باقي الأعضاء بيشوفوا "تم حذف هذه الرسالة" بدل ما الرسالة تروح فجأة.
            $table->boolean('is_removed')->default(false)->after('file_path');
            $table->foreignId('removed_by')->nullable()->after('is_removed')->constrained('users')->nullOnDelete();
            $table->timestamp('removed_at')->nullable()->after('removed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('removed_by');
            $table->dropColumn(['is_removed', 'removed_at']);
        });
    }
};
