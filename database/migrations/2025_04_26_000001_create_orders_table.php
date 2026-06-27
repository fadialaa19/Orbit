<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable()->comment('stripe, paypal, local_bank, etc.');
            $table->string('transaction_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scholarship_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

