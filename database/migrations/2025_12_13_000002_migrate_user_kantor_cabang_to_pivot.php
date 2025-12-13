<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Copy existing kantor_cabang_id into kantor_cabang_user pivot where missing
        $users = DB::table('users')->whereNotNull('kantor_cabang_id')->get(['id', 'kantor_cabang_id']);

        foreach ($users as $u) {
            $exists = DB::table('kantor_cabang_user')
                ->where('user_id', $u->id)
                ->where('kantor_cabang_id', $u->kantor_cabang_id)
                ->exists();

            if (!$exists) {
                DB::table('kantor_cabang_user')->insert([
                    'user_id' => $u->id,
                    'kantor_cabang_id' => $u->kantor_cabang_id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: keep pivot data as canonical source
    }
};
