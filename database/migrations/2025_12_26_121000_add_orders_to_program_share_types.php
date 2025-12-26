<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('program_share_types')) {
            Schema::table('program_share_types', function (Blueprint $table) {
                $table->integer('orders')->nullable()->after('key');
            });

            // set default ordering based on keys
            $mapping = [
                'dp' => 1,
                'ops_1' => 2,
                'ops_2' => 3,
                'program' => 4,
                'fee_mitra' => 5,
                'bonus' => 6,
                'championship' => 7,
            ];

            foreach ($mapping as $key => $order) {
                DB::table('program_share_types')->where('key', $key)->update(['orders' => $order]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('program_share_types')) {
            Schema::table('program_share_types', function (Blueprint $table) {
                $table->dropColumn('orders');
            });
        }
    }
};
