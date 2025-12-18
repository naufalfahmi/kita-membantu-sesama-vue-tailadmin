<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('remunerasis')) {
            Schema::dropIfExists('remunerasis');
        }
    }

    public function down()
    {
        Schema::create('remunerasis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('bulan_remunerasi')->unsigned();
            $table->integer('tahun_remunerasi');
            $table->bigInteger('gaji_pokok')->nullable();
            $table->bigInteger('take_home_pay')->default(0);
            $table->date('tanggal')->nullable();
            $table->foreignId('karyawan_id')->nullable()->constrained('users')->onDelete('set null');
            $table->uuid('kantor_cabang_id')->nullable();
            $table->timestamps();

            $table->foreign('kantor_cabang_id')
                ->references('id')
                ->on('kantor_cabang')
                ->onDelete('set null');
        });
    }
};
