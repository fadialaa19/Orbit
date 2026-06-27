<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. حذف الجدول القديم أولاً لتجنب مشاكل القيود (Foreign Keys) في SQLite
        Schema::dropIfExists('chat_messages');

        // 2. إنشاء الجدول من جديد بالهيكلية الصحيحة
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            
            // نظام الـ Polymorphic: يربط الرسالة بـ Room أو SupportTicket
            $table->unsignedBigInteger('messageable_id');
            $table->string('messageable_type');
            
            // الشخص الذي أرسل الرسالة (User أو Admin أو null للـ AI)
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->enum('sender_type', ['user', 'ai', 'admin']);
            $table->text('message_text');
            $table->string('file_path')->nullable();
            $table->timestamps();

            // فهارس لتحسين الأداء
            $table->index(['messageable_id', 'messageable_type']);
            $table->index('sender_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};