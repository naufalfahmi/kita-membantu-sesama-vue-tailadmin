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
        Schema::create('donaturs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 50)->nullable()->unique();
            $table->string('nama', 255);
            $table->json('jenis_donatur');
            $table->string('pic', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_handphone', 30)->nullable();
            $table->string('email', 255)->nullable()->unique();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif', 'pending'])->default('aktif');
            $table->foreignUuid('kantor_cabang_id')->nullable()->constrained('kantor_cabang')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['nama', 'deleted_at'], 'idx_donaturs_nama_deleted');
            $table->index(['status', 'deleted_at'], 'idx_donaturs_status_deleted');
            $table->index(['kantor_cabang_id', 'deleted_at'], 'idx_donaturs_kantor_cabang_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donaturs');
    }
};
