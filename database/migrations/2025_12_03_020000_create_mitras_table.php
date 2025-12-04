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
        Schema::create('mitras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 255);
            $table->string('email', 255)->nullable()->unique();
            $table->string('no_handphone', 30)->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan', 100)->nullable();
            $table->foreignUuid('kantor_cabang_id')->nullable()->constrained('kantor_cabang')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['nama', 'deleted_at'], 'idx_mitras_nama_deleted');
            $table->index(['kantor_cabang_id', 'deleted_at'], 'idx_mitras_kantor_cabang_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
