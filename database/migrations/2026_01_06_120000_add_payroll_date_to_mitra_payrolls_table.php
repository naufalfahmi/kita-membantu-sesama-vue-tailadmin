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
        Schema::table('mitra_payrolls', function (Blueprint $table) {
            $table->date('payroll_date')->nullable()->index()->after('program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_payrolls', function (Blueprint $table) {
            $table->dropIndex(['payroll_date']);
            $table->dropColumn('payroll_date');
        });
    }
};
