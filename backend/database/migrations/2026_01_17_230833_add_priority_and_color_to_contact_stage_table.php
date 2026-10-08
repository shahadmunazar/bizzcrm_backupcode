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
        Schema::table('contact_stage', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_stage', 'is_task_priority')) {
                $table->boolean('is_task_priority')->default(0)->after('status');
            }
            if (!Schema::hasColumn('contact_stage', 'color')) {
                $table->string('color')->nullable()->after('is_task_priority');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->dropColumn(['is_task_priority', 'color']);
        });
    }
};
