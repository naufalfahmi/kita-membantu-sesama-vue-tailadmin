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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_induk', 50)->nullable()->unique()->after('email');
            $table->string('posisi', 100)->nullable()->after('no_induk');
            $table->foreignUuid('pangkat_id')->nullable()->after('posisi')->constrained('pangkats')->nullOnDelete();
            $table->foreignUuid('tipe_absensi_id')->nullable()->after('pangkat_id')->constrained('tipe_absensi')->nullOnDelete();
            $table->string('no_handphone', 30)->nullable()->after('tipe_absensi_id');
            $table->string('nama_bank', 100)->nullable()->after('no_handphone');
            $table->string('no_rekening', 50)->nullable()->after('nama_bank');
            $table->date('tanggal_lahir')->nullable()->after('no_rekening');
            $table->string('pendidikan', 100)->nullable()->after('tanggal_lahir');
            $table->date('tanggal_masuk')->nullable()->after('pendidikan');
            $table->foreignUuid('kantor_cabang_id')->nullable()->after('tanggal_masuk')->constrained('kantor_cabang')->nullOnDelete();
            $table->string('tipe_user', 50)->nullable()->after('kantor_cabang_id');
            $table->boolean('is_active')->default(true)->after('tipe_user');
            $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
            $table->softDeletes()->after('deleted_by');

            $table->index('tipe_user');
            $table->index(['is_active', 'tipe_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_tipe_user_index');
            $table->dropIndex('users_is_active_tipe_user_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pangkat_id');
            $table->dropConstrainedForeignId('tipe_absensi_id');
            $table->dropConstrainedForeignId('kantor_cabang_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropConstrainedForeignId('deleted_by');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'no_induk',
                'posisi',
                'no_handphone',
                'nama_bank',
                'no_rekening',
                'tanggal_lahir',
                'pendidikan',
                'tanggal_masuk',
                'tipe_user',
                'is_active',
            ]);
        });
    }
};
