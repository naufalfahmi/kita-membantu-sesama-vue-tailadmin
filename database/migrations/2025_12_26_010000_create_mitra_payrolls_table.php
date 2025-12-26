<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mitra_payrolls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mitra_id')->nullable()->index();
            $table->uuid('program_id')->nullable()->index();
            $table->string('nama_mitra')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('persentase', 8, 4)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['mitra_id', 'program_id'], 'idx_mitra_payrolls_mitra_program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_payrolls');
    }
};
