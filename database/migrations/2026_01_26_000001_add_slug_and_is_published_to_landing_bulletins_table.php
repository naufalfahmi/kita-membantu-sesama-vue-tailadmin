<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_bulletins', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->boolean('is_published')->default(true)->after('file_size');
        });

        // Generate slugs for existing records
        $bulletins = DB::table('landing_bulletins')->whereNull('slug')->get();
        foreach ($bulletins as $bulletin) {
            $slug = Str::slug($bulletin->name);
            
            // Handle duplicate slugs
            $count = 1;
            $originalSlug = $slug;
            while (DB::table('landing_bulletins')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            
            DB::table('landing_bulletins')
                ->where('id', $bulletin->id)
                ->update(['slug' => $slug]);
        }

        // Make slug unique and non-nullable after backfilling
        Schema::table('landing_bulletins', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('landing_bulletins', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_published']);
        });
    }
};
