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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            // announcement: only admins can post, students read-only
            // discussion: any student can post
            $table->enum('type', ['announcement', 'discussion'])->default('discussion');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('pinned_message_id')->nullable()->constrained('chat_messages')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
