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
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            $table->string('status', 50)->default('Draft')->after('kantor_cabang_id');
            $table->index(['status', 'deleted_at'], 'idx_pengajuandanas_status_deleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            $table->dropIndex('idx_pengajuandanas_status_deleted');
            $table->dropColumn('status');
        });
    }
};
