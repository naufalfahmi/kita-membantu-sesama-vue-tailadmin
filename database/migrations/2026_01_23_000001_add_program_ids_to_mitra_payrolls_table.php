<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('mitra_payrolls') && ! Schema::hasColumn('mitra_payrolls', 'program_ids')) {
            Schema::table('mitra_payrolls', function (Blueprint $table) {
                $table->json('program_ids')->nullable()->after('program_id');
                // Note: MySQL requires generated columns to index JSON paths. Keep only program_id indexed.
                $table->index(['program_id'], 'idx_mitra_payrolls_program');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mitra_payrolls') && Schema::hasColumn('mitra_payrolls', 'program_ids')) {
            Schema::table('mitra_payrolls', function (Blueprint $table) {
                if (Schema::hasColumn('mitra_payrolls', 'program_id')) {
                    $table->dropIndex('idx_mitra_payrolls_program');
                }
                $table->dropColumn('program_ids');
            });
        }
    }
};
