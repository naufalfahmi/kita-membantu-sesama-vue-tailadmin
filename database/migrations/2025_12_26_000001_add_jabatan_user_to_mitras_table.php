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
        if (Schema::hasTable('mitras')) {
            Schema::table('mitras', function (Blueprint $table) {
                if (! Schema::hasColumn('mitras', 'jabatan_id')) {
                    $table->unsignedBigInteger('jabatan_id')->nullable()->after('kantor_cabang_id')->index();
                }
                if (! Schema::hasColumn('mitras', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('jabatan_id')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mitras')) {
            Schema::table('mitras', function (Blueprint $table) {
                if (Schema::hasColumn('mitras', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('mitras', 'jabatan_id')) {
                    $table->dropColumn('jabatan_id');
                }
            });
        }
    }
};
