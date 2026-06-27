<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['support', 'community', 'private']);
            $table->text('description')->nullable();
            $table->foreignId('creator_id')->constrained('users');
            $table->json('participants'); // array of user_ids
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};

