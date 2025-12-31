<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('pengajuan_dana_approvals')) {
            Schema::create('pengajuan_dana_approvals', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('pengajuan_dana_id');
                $table->foreign('pengajuan_dana_id')->references('id')->on('pengajuan_danas')->onDelete('cascade');
                $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('decision', 50); // Approved, Rejected, etc.
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_dana_approvals');
    }
};
