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
        // Add leader column only if not exists; otherwise ensure FK exists
        if (!Schema::hasColumn('users', 'leader_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('leader_id')->nullable()->after('kantor_cabang_id')->constrained('users')->nullOnDelete();
            });
        } else {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('leader_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Exception $e) {
                // ignore if constraint cannot be added
            }
        }

        // Pivot table for many-to-many relationship between users and kantor_cabang
        if (!Schema::hasTable('kantor_cabang_user')) {
            Schema::create('kantor_cabang_user', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('kantor_cabang_id')->constrained('kantor_cabang')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['kantor_cabang_id', 'user_id'], 'uq_kantor_user');
                $table->index(['user_id'], 'idx_kantor_user_user_id');
                $table->index(['kantor_cabang_id'], 'idx_kantor_user_kantor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'leader_id')) {
                $table->dropConstrainedForeignId('leader_id');
            }
        });

        Schema::dropIfExists('kantor_cabang_user');
    }
};
