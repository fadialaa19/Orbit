<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::dropIfExists('chat_messages'); // حذف الجدول القديم لضمان نظافة البيانات

    Schema::create('chat_messages', function (Blueprint $table) {
        $table->id();
        
        // ربط الرسالة بالمحادثة (سواء كانت Room أو SupportTicket)
        $table->unsignedBigInteger('messageable_id');
        $table->string('messageable_type');
        
        // الشخص الذي أرسل الرسالة (مستخدم أو أدمن أو null للذكاء الاصطناعي)
        $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
        
        $table->enum('sender_type', ['user', 'ai', 'admin']);
        $table->text('message_text');
        $table->string('file_path')->nullable();
        $table->timestamps();

        // فهارس لتسريع البحث
        $table->index(['messageable_id', 'messageable_type']);
        $table->index('sender_type');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};

