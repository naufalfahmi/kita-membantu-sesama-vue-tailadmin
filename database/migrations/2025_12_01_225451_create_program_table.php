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
        Schema::create('program', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_program', 255);
            $table->decimal('persentase_hak_program', 5, 2)->nullable();
            $table->decimal('persentase_hak_program_operasional', 5, 2)->nullable();
            $table->decimal('persentase_hak_championship', 5, 2)->nullable();
            $table->enum('tipe_pembagian_marketing', ['percentage', 'fixed', 'variable'])->nullable();
            $table->decimal('persentase_hak_marketing', 5, 2)->nullable();
            $table->decimal('persentase_hak_operasional_1', 5, 2)->nullable();
            $table->decimal('persentase_hak_iklan', 5, 2)->nullable();
            $table->decimal('persentase_hak_operasional_2', 5, 2)->nullable();
            $table->decimal('persentase_hak_operasional_3', 5, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes
            $table->index(['nama_program', 'deleted_at'], 'idx_program_nama_deleted');
            $table->index(['tipe_pembagian_marketing', 'deleted_at'], 'idx_program_tipe_marketing_deleted');
            $table->index(['created_by', 'deleted_at'], 'idx_program_created_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program');
    }
};
