<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chat_messages');
    }

    public function down(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['user', 'ai', 'admin']);
            $table->text('message_text');
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->index(['ticket_id', 'created_at']);
            $table->index('sender_type');
        });
    }
};

