<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('push_subscriptions')) {
            return;
        }

        if (Schema::hasColumn('push_subscriptions', 'pushify_subscriber_id')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->dropIndex(['pushify_subscriber_id']);
                $table->dropColumn('pushify_subscriber_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('push_subscriptions')) {
            return;
        }

        if (! Schema::hasColumn('push_subscriptions', 'pushify_subscriber_id')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->string('pushify_subscriber_id')->nullable()->index();
            });
        }
    }
};
