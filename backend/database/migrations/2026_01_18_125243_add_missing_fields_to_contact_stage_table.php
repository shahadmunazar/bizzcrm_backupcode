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
            if (!Schema::hasColumn('contact_stage', 'reminder_mandatory')) {
                $table->string('reminder_mandatory')->default('No');
            }
            if (!Schema::hasColumn('contact_stage', 'notification')) {
                $table->string('notification')->default('No');
            }
            if (!Schema::hasColumn('contact_stage', 'priority')) {
                $table->integer('priority')->default(0);
            }
            if (!Schema::hasColumn('contact_stage', 'is_default')) {
                $table->string('is_default')->default('No');
            }
            if (!Schema::hasColumn('contact_stage', 'is_final')) {
                $table->string('is_final')->default('No');
            }
            if (!Schema::hasColumn('contact_stage', 'display_amount_field')) {
                $table->string('display_amount_field')->default('No');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_stage', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_mandatory', 
                'notification', 
                'priority', 
                'is_default', 
                'is_final', 
                'display_amount_field'
            ]);
        });
    }
};
