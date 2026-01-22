<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('landing_programs')) {
            return;
        }

        Schema::table('landing_programs', function (Blueprint $table) {
            $table->string('slug')->nullable()->index();
        });

        $items = DB::table('landing_programs')->select('id', 'name')->get();
        foreach ($items as $item) {
            $base = Str::slug($item->name ?? 'program');
            $slug = $base ?: ('program-' . $item->id);
            $i = 1;
            while (DB::table('landing_programs')->where('slug', $slug)->where('id', '!=', $item->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('landing_programs')->where('id', $item->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('landing_programs')) {
            return;
        }

        Schema::table('landing_programs', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
