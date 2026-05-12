<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('avatar');
            }

            if (! Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('facebook_id');
            }

            if (! Schema::hasColumn('users', 'theme')) {
                $table->string('theme')->default('light')->after('profile_photo');
            }

            if (! Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('theme');
            }

            if (! Schema::hasColumn('users', 'app_preferences')) {
                $table->json('app_preferences')->nullable()->after('notification_preferences');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(
                ['phone', 'profile_photo', 'theme', 'notification_preferences', 'app_preferences'],
                fn (string $column): bool => Schema::hasColumn('users', $column)
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
