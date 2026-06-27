<?php

use App\Models\ScholarshipApplication;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('scholarship_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', [
                ScholarshipApplication::STATUS_PENDING,
                ScholarshipApplication::STATUS_PROCESSING,
                ScholarshipApplication::STATUS_APPROVED,
                ScholarshipApplication::STATUS_REJECTED,
            ])->default(ScholarshipApplication::STATUS_PENDING);

            $table->string('admission_letter_path')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'scholarship_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};

