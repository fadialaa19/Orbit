<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->longText('overview')->nullable()->after('description');
            $table->longText('conditions')->nullable()->after('overview');
            $table->longText('documents')->nullable()->after('conditions');
            $table->longText('features')->nullable()->after('documents');
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn(['overview', 'conditions', 'documents', 'features']);
        });
    }
};

