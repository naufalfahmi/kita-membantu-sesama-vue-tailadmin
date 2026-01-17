<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('program', function (Blueprint $table) {
            // Add nullable orders column first
            $table->bigInteger('orders')->nullable()->after('nama_program');
        });

        // Backfill existing rows with created_at as unix timestamp
        DB::statement("UPDATE program SET orders = UNIX_TIMESTAMP(created_at) WHERE orders IS NULL");

        // Make column not null and set default to current unix timestamp for new rows (MySQL 8+ supports expression defaults)
        // If the database does not support expression default, this statement may fail and can be adjusted manually.
        try {
            DB::statement("ALTER TABLE program MODIFY orders BIGINT NOT NULL DEFAULT (UNIX_TIMESTAMP())");
        } catch (\Exception $e) {
            // If DB doesn't support expression default, fall back to a numeric default (0)
            DB::statement("ALTER TABLE program MODIFY orders BIGINT NOT NULL DEFAULT 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program', function (Blueprint $table) {
            if (Schema::hasColumn('program', 'orders')) {
                $table->dropColumn('orders');
            }
        });
    }
};
