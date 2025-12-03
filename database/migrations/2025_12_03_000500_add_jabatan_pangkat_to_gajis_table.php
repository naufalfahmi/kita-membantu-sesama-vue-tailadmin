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
        Schema::table('gajis', function (Blueprint $table) {
            $table->uuid('jabatan_id')->nullable()->after('keterangan');
            $table->uuid('pangkat_id')->nullable()->after('jabatan_id');

            $table->index('jabatan_id');
            $table->index('pangkat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gajis', function (Blueprint $table) {
            $table->dropIndex(['jabatan_id']);
            $table->dropIndex(['pangkat_id']);
            $table->dropColumn(['jabatan_id', 'pangkat_id']);
        });
    }
};
