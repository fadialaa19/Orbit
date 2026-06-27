<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('status')->comment('Price for Apply via Us service; NULL means free grant');
            $table->string('application_url')->nullable()->after('price')->comment('External free application form URL');
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn(['price', 'application_url']);
        });
    }
};

