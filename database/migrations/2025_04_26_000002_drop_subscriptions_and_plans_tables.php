<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop subscriptions first due to foreign key referencing plans
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }

    public function down(): void
    {
        // Recreate plans table (simplified fallback)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('monthly_price', 10, 2);
            $table->json('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->string('color_theme')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Recreate subscriptions table (simplified fallback)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('transfer_from');
            $table->string('bank_wallet');
            $table->string('receipt_image');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }
};

