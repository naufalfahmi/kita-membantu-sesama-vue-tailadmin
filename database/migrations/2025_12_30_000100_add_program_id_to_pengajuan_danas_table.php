<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // make idempotent: only add column/index/foreign if not exists
        if (! Schema::hasColumn('pengajuan_danas', 'program_id')) {
            Schema::table('pengajuan_danas', function (Blueprint $table) {
                $table->uuid('program_id')->nullable()->after('submission_type');
                $table->index(['program_id', 'deleted_at'], 'idx_pengajuan_program_deleted');
            });

            // add foreign key in a separate statement to avoid issues on some MySQL setups
            Schema::table('pengajuan_danas', function (Blueprint $table) {
                $table->foreign('program_id')->references('id')->on('program')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pengajuan_danas', 'program_id')) {
            Schema::table('pengajuan_danas', function (Blueprint $table) {
                // drop foreign if exists
                try {
                    $table->dropForeign(['program_id']);
                } catch (\Exception $e) {
                    // ignore if foreign doesn't exist
                }

                try {
                    $table->dropIndex('idx_pengajuan_program_deleted');
                } catch (\Exception $e) {
                    // ignore
                }

                $table->dropColumn('program_id');
            });
        }
    }
};
