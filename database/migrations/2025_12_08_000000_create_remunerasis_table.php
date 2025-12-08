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
        Schema::create('remunerasis', function (Blueprint $table) {
            // Use UUID primary key to match other tables
            $table->uuid('id')->primary();
            $table->tinyInteger('bulan_remunerasi')->unsigned();
            $table->integer('tahun_remunerasi');
            $table->bigInteger('gaji_pokok')->nullable();
            $table->bigInteger('take_home_pay')->default(0);
            $table->date('tanggal')->nullable();
            // reference to user (karyawan) - users.id is bigint
            $table->foreignId('karyawan_id')->nullable()->constrained('users')->onDelete('set null');
            // reference kantor_cabang table which uses uuid id
            $table->uuid('kantor_cabang_id')->nullable();
            $table->timestamps();

            $table->foreign('kantor_cabang_id')
                ->references('id')
                ->on('kantor_cabang')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remunerasis');
    }
};
