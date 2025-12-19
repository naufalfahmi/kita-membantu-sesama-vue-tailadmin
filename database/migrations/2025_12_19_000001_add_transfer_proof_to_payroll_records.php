<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->string('transfer_proof')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn('transfer_proof');
        });
    }
};
