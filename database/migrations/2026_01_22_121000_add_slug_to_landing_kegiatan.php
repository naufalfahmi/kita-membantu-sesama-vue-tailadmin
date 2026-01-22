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
        if (! Schema::hasTable('landing_kegiatan')) {
            return;
        }

        Schema::table('landing_kegiatan', function (Blueprint $table) {
            $table->string('slug')->nullable()->index();
        });

        $items = DB::table('landing_kegiatan')->select('id', 'title')->get();
        foreach ($items as $item) {
            $base = Str::slug($item->title ?? 'kegiatan');
            $slug = $base ?: ('kegiatan-' . $item->id);
            $i = 1;
            while (DB::table('landing_kegiatan')->where('slug', $slug)->where('id', '!=', $item->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('landing_kegiatan')->where('id', $item->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('landing_kegiatan')) {
            return;
        }

        Schema::table('landing_kegiatan', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
