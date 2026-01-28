<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('landing_profiles', function (Blueprint $table) {
            $table->string('transparency_title')->nullable()->after('hero_image');
            $table->text('transparency_description')->nullable()->after('transparency_title');
            
            $table->string('transparency_kantor_cabang_text')->nullable()->after('transparency_description');
            $table->integer('transparency_kantor_cabang_total')->default(0)->after('transparency_kantor_cabang_text');
            
            $table->string('transparency_donatur_text')->nullable()->after('transparency_kantor_cabang_total');
            $table->integer('transparency_donatur_total')->default(0)->after('transparency_donatur_text');
            
            $table->string('transparency_fundraiser_text')->nullable()->after('transparency_donatur_total');
            $table->integer('transparency_fundraiser_total')->default(0)->after('transparency_fundraiser_text');
            
            $table->string('transparency_penggalangan_dana_text')->nullable()->after('transparency_fundraiser_total');
            $table->integer('transparency_penggalangan_dana_total')->default(0)->after('transparency_penggalangan_dana_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'transparency_title',
                'transparency_description',
                'transparency_kantor_cabang_text',
                'transparency_kantor_cabang_total',
                'transparency_donatur_text',
                'transparency_donatur_total',
                'transparency_fundraiser_text',
                'transparency_fundraiser_total',
                'transparency_penggalangan_dana_text',
                'transparency_penggalangan_dana_total'
            ]);
        });
    }
};
