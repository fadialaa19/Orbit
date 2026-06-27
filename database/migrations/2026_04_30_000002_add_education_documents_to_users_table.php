<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Education - High School (Required)
            $table->string('high_school_name')->nullable()->after('graduation_year');
            $table->string('high_school_country')->nullable()->after('high_school_name');
            $table->float('high_school_gpa')->nullable()->after('high_school_country');
            $table->string('high_school_certificate')->nullable()->after('high_school_gpa');
            
            // Education - Diploma (Optional)
            $table->string('diploma_institute')->nullable()->after('university');
            $table->string('diploma_country')->nullable()->after('diploma_institute');
            $table->integer('diploma_year')->nullable()->after('diploma_country');
            $table->float('diploma_gpa')->nullable()->after('diploma_year');
            
            // Education - Bachelor's (Optional)
            $table->string('bachelor_university')->nullable()->after('diploma_gpa');
            $table->string('bachelor_country')->nullable()->after('bachelor_university');
            $table->integer('bachelor_year')->nullable()->after('bachelor_country');
            $table->string('bachelor_degree')->nullable()->after('bachelor_year');
            $table->float('bachelor_gpa')->nullable()->after('bachelor_degree');
            
            // Personal IDs
            $table->string('national_id')->nullable()->after('phone');
            $table->string('passport_number')->nullable()->after('national_id');
            $table->date('passport_expiry')->nullable()->after('passport_number');
            $table->string('passport_country')->nullable()->after('passport_expiry');
            
            // Documents (JSON)
            $table->json('required_documents')->nullable()->after('documents');
            $table->json('optional_documents')->nullable()->after('required_documents');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'high_school_name', 'high_school_country', 'high_school_gpa', 'high_school_certificate',
                'diploma_institute', 'diploma_country', 'diploma_year', 'diploma_gpa',
                'bachelor_university', 'bachelor_country', 'bachelor_year', 'bachelor_degree', 'bachelor_gpa',
                'national_id', 'passport_number', 'passport_expiry', 'passport_country',
                'required_documents', 'optional_documents'
            ]);
        });
    }
};
