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
        Schema::create('karyawan_mitra_transaksi_visibilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('karyawan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('visible_mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['karyawan_id', 'visible_mitra_id'], 'uq_karyawan_mitra_transaksi_visibility_pair');
            $table->index(['karyawan_id', 'visible_mitra_id', 'deleted_at'], 'idx_karyawan_mitra_transaksi_visibility_deleted');
        });

        Schema::create('karyawan_mitra_donatur_visibilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('karyawan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('visible_mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['karyawan_id', 'visible_mitra_id'], 'uq_karyawan_mitra_donatur_visibility_pair');
            $table->index(['karyawan_id', 'visible_mitra_id', 'deleted_at'], 'idx_karyawan_mitra_donatur_visibility_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_mitra_donatur_visibilities');
        Schema::dropIfExists('karyawan_mitra_transaksi_visibilities');
    }
};
