<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('endpoint', 500);
            $table->string('auth_key', 191)->nullable();
            $table->string('p256dh', 191)->nullable();
            $table->string('pushify_subscriber_id')->nullable()->index();
            $table->string('device')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'endpoint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
