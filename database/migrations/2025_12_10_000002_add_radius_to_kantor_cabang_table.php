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
        Schema::table('kantor_cabang', function (Blueprint $table) {
            $table->integer('radius')->default(100)->after('longitude')->comment('Allowed radius for attendance in meters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kantor_cabang', function (Blueprint $table) {
            $table->dropColumn('radius');
        });
    }
};
