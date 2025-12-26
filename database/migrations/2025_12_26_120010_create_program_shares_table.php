<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_shares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->uuid('program_share_type_id');
            $table->enum('type', ['percentage', 'nominal'])->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('program')->onDelete('cascade');
            $table->foreign('program_share_type_id')->references('id')->on('program_share_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_shares');
    }
};
