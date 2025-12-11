<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Some DB drivers (sqlite used in tests) do not support MODIFY syntax.
        // Only run the ALTER for MySQL/Postgres. For sqlite, skip (in tests the schema is fresh anyway).
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'])) {
            DB::statement('ALTER TABLE landing_proposals MODIFY COLUMN `file` TEXT NULL');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'])) {
            DB::statement('ALTER TABLE landing_proposals MODIFY COLUMN `file` VARCHAR(255) NULL');
        }
    }
};
