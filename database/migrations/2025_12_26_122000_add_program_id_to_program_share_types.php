<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('program_share_types')) {
            Schema::table('program_share_types', function (Blueprint $table) {
                $table->uuid('program_id')->nullable()->after('orders');
                $table->index('program_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('program_share_types')) {
            Schema::table('program_share_types', function (Blueprint $table) {
                $table->dropIndex(['program_id']);
                $table->dropColumn('program_id');
            });
        }
    }
};
