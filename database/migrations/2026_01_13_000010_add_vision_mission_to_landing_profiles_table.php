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
            $table->string('vision_title')->nullable()->after('about');
            $table->text('vision_description')->nullable()->after('vision_title');
            $table->text('mission_description')->nullable()->after('vision_description');
            $table->string('vision_mission_image')->nullable()->after('mission_description');
            $table->json('features')->nullable()->after('vision_mission_image');

            $table->string('cta_title')->nullable()->after('features');
            $table->text('cta_description')->nullable()->after('cta_title');
            $table->boolean('cta_button_active')->default(false)->after('cta_description');
            $table->string('cta_button_link')->nullable()->after('cta_button_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_profiles', function (Blueprint $table) {
            // Some DB drivers require individual dropColumn calls
            if (Schema::hasColumn('landing_profiles', 'vision_title')) {
                $table->dropColumn('vision_title');
            }
            if (Schema::hasColumn('landing_profiles', 'vision_description')) {
                $table->dropColumn('vision_description');
            }
            if (Schema::hasColumn('landing_profiles', 'mission_description')) {
                $table->dropColumn('mission_description');
            }
            if (Schema::hasColumn('landing_profiles', 'vision_mission_image')) {
                $table->dropColumn('vision_mission_image');
            }
            if (Schema::hasColumn('landing_profiles', 'features')) {
                $table->dropColumn('features');
            }
            if (Schema::hasColumn('landing_profiles', 'cta_title')) {
                $table->dropColumn('cta_title');
            }
            if (Schema::hasColumn('landing_profiles', 'cta_description')) {
                $table->dropColumn('cta_description');
            }
            if (Schema::hasColumn('landing_profiles', 'cta_button_active')) {
                $table->dropColumn('cta_button_active');
            }
            if (Schema::hasColumn('landing_profiles', 'cta_button_link')) {
                $table->dropColumn('cta_button_link');
            }
        });
    }
};
