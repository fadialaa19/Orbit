<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::table('users', function (Blueprint $table) {
            // Education - High School additional fields
            $table->integer('high_school_year')->nullable()->after('high_school_gpa');
            $table->string('high_school_branch')->nullable()->after('high_school_year');
            
            // Education - Diploma additional field
            $table->string('diploma_degree')->nullable()->after('diploma_gpa');
            
            // Education - Master's (Optional)
            $table->string('master_university')->nullable()->after('bachelor_gpa');
            $table->string('master_country')->nullable()->after('master_university');
            $table->integer('master_year')->nullable()->after('master_country');
            $table->string('master_degree')->nullable()->after('master_year');
            $table->float('master_gpa')->nullable()->after('master_degree');
            $table->string('master_certificate')->nullable()->after('master_gpa');
        });
    }

public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'high_school_year',
                'high_school_branch',
                'diploma_degree',
                'master_university',
                'master_country',
                'master_year',
                'master_degree',
                'master_gpa',
                'master_certificate',
            ]);
        });
    }
};
