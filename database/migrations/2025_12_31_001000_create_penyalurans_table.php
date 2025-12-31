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
        Schema::create('penyalurans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pengajuan_dana_id')->nullable();
            $table->string('program_name')->nullable();
            $table->string('pic')->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address')->nullable();
            $table->text('report')->nullable();
            $table->bigInteger('amount')->default(0);
            $table->uuid('kantor_cabang_id')->nullable();
            $table->foreign('kantor_cabang_id')->references('id')->on('kantor_cabang')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['pengajuan_dana_id', 'kantor_cabang_id'], 'idx_penyalurans_pengajuan_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyalurans');
    }
};
