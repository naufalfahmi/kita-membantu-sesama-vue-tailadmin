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
        Schema::table('donaturs', function (Blueprint $table) {
            $table->uuid('mitra_id')->nullable()->after('kantor_cabang_id');
            $table->foreign('mitra_id')->references('id')->on('mitras')->nullOnDelete();
            $table->index(['mitra_id', 'deleted_at'], 'idx_donaturs_mitra_id_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donaturs', function (Blueprint $table) {
            $table->dropIndex('idx_donaturs_mitra_id_deleted');
            $table->dropForeign(['mitra_id']);
            $table->dropColumn('mitra_id');
        });
    }
};
