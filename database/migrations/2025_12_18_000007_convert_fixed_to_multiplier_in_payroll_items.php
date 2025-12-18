<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert legacy 'fixed' qty_type to 'multiplier' to align with new semantics
        DB::table('payroll_items')->where('qty_type', 'fixed')->update(['qty_type' => 'multiplier']);
    }

    public function down(): void
    {
        // revert back to 'fixed' if necessary
        DB::table('payroll_items')->where('qty_type', 'multiplier')->update(['qty_type' => 'fixed']);
    }
};
