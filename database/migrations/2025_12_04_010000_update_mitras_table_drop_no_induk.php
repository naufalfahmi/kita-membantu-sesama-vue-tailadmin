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
        Schema::table('mitras', function (Blueprint $table) {
            if (Schema::hasColumn('mitras', 'no_induk')) {
                $table->dropColumn('no_induk');
            }

            if (Schema::hasColumn('mitras', 'tanggal_bergabung')) {
                $table->dropColumn('tanggal_bergabung');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            if (! Schema::hasColumn('mitras', 'no_induk')) {
                $table->string('no_induk', 50)->after('id');
            }

            if (! Schema::hasColumn('mitras', 'tanggal_bergabung')) {
                $table->date('tanggal_bergabung')->nullable()->after('pendidikan');
            }
        });
    }
};
