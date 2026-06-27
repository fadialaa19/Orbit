<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->string('financial_value')->nullable()->after('application_url');
            $table->integer('applicants_count')->default(0)->after('financial_value');
            $table->json('recommended_tags')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn(['financial_value', 'applicants_count', 'recommended_tags']);
        });
    }
};

