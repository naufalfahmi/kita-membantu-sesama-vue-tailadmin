<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('month')->unsigned();
            $table->integer('year');
            $table->enum('status', ['draft', 'generated', 'transferred'])->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['year', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_periods');
    }
};
