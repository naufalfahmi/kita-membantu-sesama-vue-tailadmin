<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip destructive column drops on SQLite (test env) which does not support them
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasTable('program')) {
            $cols = [
                'persentase_hak_program',
                'persentase_hak_program_operasional',
                'persentase_hak_championship',
                'tipe_pembagian_marketing',
                'persentase_hak_marketing',
                'persentase_hak_operasional_1',
                'persentase_hak_iklan',
                'persentase_hak_operasional_2',
                'persentase_hak_operasional_3',
                'jumlah_persentase',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('program', $col)) {
                    Schema::table('program', function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('program', function (Blueprint $table) {
            $table->decimal('persentase_hak_program', 5, 2)->nullable();
            $table->decimal('persentase_hak_program_operasional', 5, 2)->nullable();
            $table->decimal('persentase_hak_championship', 5, 2)->nullable();
            $table->enum('tipe_pembagian_marketing', ['percentage','nominal'])->nullable();
            $table->decimal('persentase_hak_marketing', 5, 2)->nullable();
            $table->decimal('persentase_hak_operasional_1', 5, 2)->nullable();
            $table->decimal('persentase_hak_iklan', 5, 2)->nullable();
            $table->decimal('persentase_hak_operasional_2', 5, 2)->nullable();
            $table->decimal('persentase_hak_operasional_3', 5, 2)->nullable();
            $table->decimal('jumlah_persentase', 5, 2)->nullable();
        });
    }
};
