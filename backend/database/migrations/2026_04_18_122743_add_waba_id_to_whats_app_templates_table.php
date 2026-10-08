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
        Schema::table('whats_app_templates', function (Blueprint $table) {
            $table->string('waba_id')->nullable()->after('box_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whats_app_templates', function (Blueprint $table) {
            $table->dropColumn('waba_id');
        });
    }
};
