<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_profiles', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('cta_button_link');
            $table->text('hero_description')->nullable()->after('hero_title');
            $table->boolean('hero_button_active')->default(false)->after('hero_description');
            $table->string('hero_button_link')->nullable()->after('hero_button_active');
            $table->boolean('hero_whatsapp_active')->default(false)->after('hero_button_link');
            $table->string('hero_whatsapp_number')->nullable()->after('hero_whatsapp_active');
            $table->string('hero_image')->nullable()->after('hero_whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_description',
                'hero_button_active',
                'hero_button_link',
                'hero_whatsapp_active',
                'hero_whatsapp_number',
                'hero_image',
            ]);
        });
    }
};
