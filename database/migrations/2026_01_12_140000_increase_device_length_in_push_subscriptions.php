<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_subscriptions')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->string('device', 500)->nullable()->change();
                $table->string('user_agent', 500)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('push_subscriptions')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->string('device')->nullable()->change();
                $table->string('user_agent')->nullable()->change();
            });
        }
    }
};
