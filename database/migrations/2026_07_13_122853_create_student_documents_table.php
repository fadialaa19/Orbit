<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Fixed slug (passport, national_id, ...) for documents uploaded via
            // the predefined checklist, or null for a student's own custom upload.
            $table->string('category')->nullable();
            $table->string('label');
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        // Migrate any documents already uploaded under the old JSON columns so
        // students don't lose what they already submitted.
        $requiredLabels = [
            'passport' => 'جواز السفر',
            'national_id' => 'الهوية الشخصية',
            'high_school_cert' => 'شهادة الثانوية',
            'birth_cert' => 'شهادة الميلاد',
            'cv' => 'السيرة الذاتية (CV)',
        ];
        $optionalLabels = [
            'language_cert' => 'شهادة لغة',
            'courses_cert' => 'شهادة دورات',
            'recommendation' => 'خطاب توصية',
            'intent_letter' => 'خطاب نية',
        ];

        $now = now();
        $rows = [];

        foreach (DB::table('users')->where('role', 'student')->get(['id', 'required_documents', 'optional_documents']) as $user) {
            foreach ([$requiredLabels, $optionalLabels] as $labelSet) {
                $isRequired = $labelSet === $requiredLabels;
                $data = json_decode($isRequired ? $user->required_documents : $user->optional_documents, true) ?? [];

                foreach ($labelSet as $key => $label) {
                    if (!empty($data[$key])) {
                        $rows[] = [
                            'user_id' => $user->id,
                            'category' => $key,
                            'label' => $label,
                            'file_path' => $data[$key],
                            'status' => 'pending',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('student_documents')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
