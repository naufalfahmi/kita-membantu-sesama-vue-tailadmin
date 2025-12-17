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
        Schema::table('donaturs', function (Blueprint $table) {
            $table->string('provinsi')->nullable()->after('alamat');
            $table->string('kota_kab')->nullable()->after('provinsi');
            $table->string('kecamatan')->nullable()->after('kota_kab');
            $table->string('kelurahan')->nullable()->after('kecamatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donaturs', function (Blueprint $table) {
            $table->dropColumn(['provinsi', 'kota_kab', 'kecamatan', 'kelurahan']);
        });
    }
};
