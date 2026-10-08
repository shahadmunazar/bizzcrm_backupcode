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
        Schema::table('crm_sources', function (Blueprint $table) {
            $table->enum('is_default', ['No', 'Yes'])->default('No');
            $table->text('groups')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_sources', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'groups']);
        });
    }
};
