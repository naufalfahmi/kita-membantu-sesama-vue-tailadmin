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
        if (! Schema::hasTable('pengajuan_dana_disbursements')) {
            Schema::create('pengajuan_dana_disbursements', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('pengajuan_dana_id')->nullable()->index();
                $table->uuid('transaksi_id')->nullable()->index();
                $table->uuid('program_id')->nullable()->index();
                $table->bigInteger('amount')->unsigned();
                $table->date('tanggal_disburse')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['program_id', 'tanggal_disburse'], 'idx_pengajuan_disbursement_program_tanggal');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengajuan_dana_disbursements')) {
            Schema::dropIfExists('pengajuan_dana_disbursements');
        }
    }
};
