<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->uuid('base_item_id')->nullable()->after('amount');
            $table->foreign('base_item_id')->references('id')->on('payroll_items')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropForeign(['base_item_id']);
            $table->dropColumn('base_item_id');
        });
    }
};
