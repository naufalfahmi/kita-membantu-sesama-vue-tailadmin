<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_share_types', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('key');
            $table->index(['alias', 'deleted_at'], 'idx_program_share_types_alias');
        });
    }

    public function down(): void
    {
        Schema::table('program_share_types', function (Blueprint $table) {
            $table->dropIndex('idx_program_share_types_alias');
            $table->dropColumn('alias');
        });
    }
};
