<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddSlugToLandingKegiatans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('landing_kegiatans')) {
            return;
        }

        Schema::table('landing_kegiatans', function (Blueprint $table) {
            $table->string('slug')->nullable()->index();
        });

        // Backfill existing rows with a slug derived from title
        $items = DB::table('landing_kegiatans')->select('id', 'title')->get();
        foreach ($items as $item) {
            $base = Str::slug($item->title ?? 'kegiatan');
            $slug = $base ?: ('kegiatan-' . $item->id);
            $i = 1;
            // ensure uniqueness (skip current row)
            while (DB::table('landing_kegiatans')->where('slug', $slug)->where('id', '!=', $item->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('landing_kegiatans')->where('id', $item->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('landing_kegiatans')) {
            return;
        }

        Schema::table('landing_kegiatans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
