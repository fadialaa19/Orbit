<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'ai_summary')) {
                $table->text('ai_summary')->nullable()->after('status');
            }
            if (!Schema::hasColumn('support_tickets', 'chat_history')) {
                $table->json('chat_history')->nullable()->after('ai_summary');
            }
            if (!Schema::hasColumn('support_tickets', 'last_reply_at')) {
                $table->timestamp('last_reply_at')->nullable()->after('chat_history');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['ai_summary', 'chat_history', 'last_reply_at']);
        });
    }
};

