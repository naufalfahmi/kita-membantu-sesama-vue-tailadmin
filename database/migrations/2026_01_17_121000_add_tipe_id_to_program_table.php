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
        Schema::table('program', function (Blueprint $table) {
            $table->foreignUuid('tipe_id')->nullable()->constrained('tipe_program')->nullOnDelete();
            $table->index(['tipe_id', 'deleted_at'], 'idx_program_tipe_id_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program', function (Blueprint $table) {
            if (Schema::hasColumn('program', 'tipe_id')) {
                // drop foreign key, index, and column
                $table->dropForeign(['tipe_id']);
                $table->dropIndex('idx_program_tipe_id_deleted');
                $table->dropColumn('tipe_id');
            }
        });
    }
};
