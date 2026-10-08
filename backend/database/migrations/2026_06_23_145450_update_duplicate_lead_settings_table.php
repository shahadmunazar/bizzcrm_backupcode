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
        // 1. Alter lead_required_field to include 'both'
        DB::statement("ALTER TABLE boxes MODIFY COLUMN lead_required_field ENUM('email', 'phone', 'both') NOT NULL DEFAULT 'email'");

        // 2. Add duplicate_of_id column to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('duplicate_of_id')->nullable()->after('duplicate_leads');
            
            // Note: Since this is an existing table that may have bad data or we might just want to keep it simple,
            // we will add a foreign key constraint.
            $table->foreign('duplicate_of_id')->references('id')->on('leads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['duplicate_of_id']);
            $table->dropColumn('duplicate_of_id');
        });

        DB::statement("ALTER TABLE boxes MODIFY COLUMN lead_required_field ENUM('email', 'phone') NOT NULL DEFAULT 'email'");
    }
};
