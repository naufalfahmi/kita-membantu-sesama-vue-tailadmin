<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing data first
        DB::table('program')
            ->where('tipe_pembagian_marketing', 'fixed')
            ->orWhere('tipe_pembagian_marketing', 'variable')
            ->update(['tipe_pembagian_marketing' => null]);

        // For MySQL, we need to use raw SQL to modify enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE program MODIFY COLUMN tipe_pembagian_marketing ENUM('percentage', 'nominal') NULL");
        } else {
            // For other databases like PostgreSQL, use schema modification
            Schema::table('program', function (Blueprint $table) {
                $table->enum('tipe_pembagian_marketing', ['percentage', 'nominal'])->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert existing data
        DB::table('program')
            ->where('tipe_pembagian_marketing', 'nominal')
            ->update(['tipe_pembagian_marketing' => null]);

        // Revert enum to original
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE program MODIFY COLUMN tipe_pembagian_marketing ENUM('percentage', 'fixed', 'variable') NULL");
        } else {
            Schema::table('program', function (Blueprint $table) {
                $table->enum('tipe_pembagian_marketing', ['percentage', 'fixed', 'variable'])->nullable()->change();
            });
        }
    }
};
