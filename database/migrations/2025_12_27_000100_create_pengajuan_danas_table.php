<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_danas', function (Blueprint $table) {
            // UUID primary key to follow project conventions
            $table->uuid('id')->primary();

            // Pengaju / fundraiser (references users.id)
            $table->foreignId('fundraiser_id')->nullable()->constrained('users')->nullOnDelete();

            // Tipe pengajuan (operasional, program, kegiatan, dll.)
            $table->string('submission_type', 100)->nullable();

            // Nominal
            $table->bigInteger('amount')->unsigned()->default(0);

            // Tanggal digunakan
            $table->date('used_at')->nullable();

            // Tujuan pengajuan
            $table->text('purpose')->nullable();

            // Kantor cabang (references kantor_cabang.id which is UUID)
            $table->uuid('kantor_cabang_id')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Foreign key for kantor_cabang (uuid)
            $table->foreign('kantor_cabang_id')
                ->references('id')
                ->on('kantor_cabang')
                ->nullOnDelete();

            // Useful indexes for queries
            $table->index(['kantor_cabang_id', 'deleted_at'], 'idx_pengajuandanas_kantor_deleted');
            $table->index(['submission_type', 'deleted_at'], 'idx_pengajuandanas_type_deleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_danas');
    }
};
