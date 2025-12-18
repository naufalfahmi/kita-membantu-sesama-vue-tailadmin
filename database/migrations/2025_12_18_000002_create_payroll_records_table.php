<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_period_id');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->onDelete('cascade');

            // employee reference (users.id is bigint)
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('status', ['pending', 'locked', 'transferred'])->default('pending');
            $table->bigInteger('total_amount')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payroll_period_id', 'employee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_records');
    }
};
