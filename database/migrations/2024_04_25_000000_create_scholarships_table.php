<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('country');
            $table->string('university');
            $table->date('deadline');
            $table->text('description');
            $table->json('coverage'); // e.g. ["full_tuition": true, "living_expenses": true]
            $table->string('category'); // bachelor/master/phd
            $table->json('tags')->nullable(); // AI matching tags
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};

