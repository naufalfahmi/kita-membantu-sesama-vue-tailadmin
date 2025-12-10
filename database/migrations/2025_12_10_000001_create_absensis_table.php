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
        Schema::create('absensis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('tipe_absensi_id')->nullable()->constrained('tipe_absensi')->nullOnDelete();
            $table->foreignUuid('kantor_cabang_id')->nullable()->constrained('kantor_cabang')->nullOnDelete();
            
            // Clock in data
            $table->timestamp('jam_masuk')->nullable();
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->decimal('jarak_masuk', 10, 2)->nullable()->comment('Distance from office in meters');
            
            // Clock out data
            $table->timestamp('jam_keluar')->nullable();
            $table->decimal('latitude_keluar', 10, 8)->nullable();
            $table->decimal('longitude_keluar', 11, 8)->nullable();
            $table->decimal('jarak_keluar', 10, 2)->nullable()->comment('Distance from office in meters');
            
            // Work summary
            $table->decimal('total_jam_kerja', 5, 2)->nullable()->comment('Total work hours');
            $table->enum('status', ['hadir', 'terlambat', 'pulang_awal', 'tidak_hadir', 'izin', 'sakit', 'cuti'])->default('hadir');
            $table->text('catatan')->nullable();
            $table->text('alasan')->nullable()->comment('Reason if work hours less than minimum or late');
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'deleted_at'], 'idx_absensis_user_deleted');
            $table->index(['jam_masuk', 'deleted_at'], 'idx_absensis_jam_masuk_deleted');
            $table->index(['status', 'deleted_at'], 'idx_absensis_status_deleted');
            $table->index(['tipe_absensi_id', 'deleted_at'], 'idx_absensis_tipe_deleted');
            $table->index(['kantor_cabang_id', 'deleted_at'], 'idx_absensis_kantor_deleted');
            $table->index(['user_id', 'jam_masuk'], 'idx_absensis_user_jam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
