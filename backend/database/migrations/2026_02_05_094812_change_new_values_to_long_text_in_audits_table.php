<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE audits MODIFY old_values LONGTEXT NULL');
        DB::statement('ALTER TABLE audits MODIFY new_values LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // DB::statement('ALTER TABLE audits MODIFY old_values TEXT NULL');
        // DB::statement('ALTER TABLE audits MODIFY new_values TEXT NULL');
    }
};
