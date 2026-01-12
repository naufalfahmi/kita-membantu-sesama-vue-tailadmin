<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_subscriptions') && ! Schema::hasColumn('push_subscriptions', 'onesignal_player_id')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->string('onesignal_player_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('push_subscriptions') && Schema::hasColumn('push_subscriptions', 'onesignal_player_id')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->dropIndex(['onesignal_player_id']);
                $table->dropColumn('onesignal_player_id');
            });
        }
    }
};
