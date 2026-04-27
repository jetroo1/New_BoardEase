<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_one')->constrained('users')->cascadeOnDelete();
            $table->foreignId('participant_two')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['participant_one', 'participant_two']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};