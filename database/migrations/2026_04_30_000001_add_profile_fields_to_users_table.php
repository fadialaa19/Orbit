<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->date('birthdate')->nullable()->after('phone');
            $table->string('country')->nullable()->after('birthdate');
            $table->string('city')->nullable()->after('country');
            $table->string('degree')->nullable()->after('city');
            $table->string('field_of_study')->nullable()->after('degree');
            $table->string('university')->nullable()->after('field_of_study');
            $table->integer('graduation_year')->nullable()->after('university');
            $table->text('bio')->nullable()->after('graduation_year');
            $table->string('avatar')->nullable()->after('bio');
            $table->json('languages')->nullable()->after('avatar');
            $table->json('achievements')->nullable()->after('languages');
            $table->json('documents')->nullable()->after('achievements');
            
            //Profile completion tracking
            $table->integer('profile_completion')->default(0)->after('documents');
            $table->integer('xp')->default(0)->after('profile_completion');
            $table->integer('level')->default(1)->after('xp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'birthdate', 'country', 'city', 'degree', 
                'field_of_study', 'university', 'graduation_year', 'bio', 'avatar',
                'languages', 'achievements', 'documents', 'profile_completion', 'xp', 'level'
            ]);
        });
    }
};
