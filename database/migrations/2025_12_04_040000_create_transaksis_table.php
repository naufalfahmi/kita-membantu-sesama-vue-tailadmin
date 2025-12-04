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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 50)->nullable()->unique();
            $table->foreignUuid('kantor_cabang_id')->nullable()->constrained('kantor_cabang')->nullOnDelete();
            $table->foreignUuid('donatur_id')->nullable()->constrained('donaturs')->nullOnDelete();
            $table->foreignUuid('program_id')->nullable()->constrained('program')->nullOnDelete();
            $table->foreignId('fundraiser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->date('tanggal_transaksi');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'verified', 'cancelled'])->default('pending');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['kode', 'deleted_at'], 'idx_transaksis_kode_deleted');
            $table->index(['kantor_cabang_id', 'deleted_at'], 'idx_transaksis_kantor_cabang_deleted');
            $table->index(['donatur_id', 'deleted_at'], 'idx_transaksis_donatur_deleted');
            $table->index(['program_id', 'deleted_at'], 'idx_transaksis_program_deleted');
            $table->index(['fundraiser_id', 'deleted_at'], 'idx_transaksis_fundraiser_deleted');
            $table->index(['tanggal_transaksi', 'deleted_at'], 'idx_transaksis_tanggal_deleted');
            $table->index(['status', 'deleted_at'], 'idx_transaksis_status_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
