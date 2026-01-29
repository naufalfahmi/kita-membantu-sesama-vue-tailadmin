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
        Schema::create('karyawan_absensi_visibilities', function (Blueprint $create) {
            $create->uuid('id')->primary();
            $create->unsignedBigInteger('karyawan_id');
            $create->unsignedBigInteger('visible_karyawan_id');
            $create->unsignedBigInteger('created_by')->nullable();
            $create->unsignedBigInteger('updated_by')->nullable();
            $create->unsignedBigInteger('deleted_by')->nullable();
            $create->timestamps();
            $create->softDeletes();

            $create->foreign('karyawan_id')->references('id')->on('users')->onDelete('cascade');
            $create->foreign('visible_karyawan_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_absensi_visibilities');
    }
};
