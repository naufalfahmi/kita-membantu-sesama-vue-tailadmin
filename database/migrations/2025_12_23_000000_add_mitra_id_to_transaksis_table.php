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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignUuid('mitra_id')->nullable()->after('program_id')->constrained('mitras')->nullOnDelete();
            $table->index(['mitra_id', 'deleted_at'], 'idx_transaksis_mitra_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex('idx_transaksis_mitra_deleted');
            $table->dropForeignKeyIfExists('transaksis_mitra_id_foreign');
            $table->dropColumn('mitra_id');
        });
    }
};
