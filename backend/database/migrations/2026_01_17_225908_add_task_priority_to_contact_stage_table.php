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
            $table->boolean('is_priority')->default(0)->after('status');
            $table->string('color')->nullable()->after('is_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->dropColumn(['is_priority', 'color']);
        });
    }
};
