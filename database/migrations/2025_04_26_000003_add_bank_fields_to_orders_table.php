<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_method')->comment('Name of local bank used for transfer');
            $table->string('transfer_from')->nullable()->after('bank_name')->comment('Account holder name');
            $table->string('receipt_image')->nullable()->after('transfer_from')->comment('Uploaded bank transfer receipt');
            $table->text('admin_notes')->nullable()->after('receipt_image');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'transfer_from', 'receipt_image', 'admin_notes']);
        });
    }
};

