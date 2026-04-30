<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chat Upgrade Migration
 * 
 * Adds:
 * - messages.image_path      → for image messages
 * - messages.seen_at         → alias rename of read_at (kept as read_at, just add seen_at as alias)
 * - users.last_seen_at       → for online/offline status
 */
return new class extends Migration
{
    public function up(): void
    {
        // Add image_path to messages (for image sending feature)
        Schema::table('messages', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content');
        });

        // Add last_seen_at to users (for online/offline status)
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });
    }
};
