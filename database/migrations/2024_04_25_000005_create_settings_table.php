<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color')->default('#6366f1');
            $table->json('payment_gateways')->nullable();
            $table->text('ai_api_key')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->timestamps();
        });

        // Default settings
        \DB::table('settings')->insert([
            'site_name' => 'BEDOON QUYOOD',
            'primary_color' => '#6366f1',
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};

