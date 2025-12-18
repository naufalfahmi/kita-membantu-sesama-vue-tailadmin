<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_record_id');
            $table->foreign('payroll_record_id')->references('id')->on('payroll_records')->onDelete('cascade');

            $table->string('description');
            $table->decimal('qty', 15, 4)->default(0);
            $table->enum('qty_type', ['fixed', 'percent', 'multiplier'])->default('fixed');
            $table->string('unit')->nullable();
            $table->decimal('unit_value', 20, 2)->default(0);
            $table->bigInteger('amount')->default(0);
            $table->integer('order_index')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payroll_record_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_items');
    }
};
