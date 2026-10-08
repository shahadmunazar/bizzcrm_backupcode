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
        Schema::table('boxes', function (Blueprint $table) {
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable()->after('company_email');
            $table->string('fax_number')->nullable()->after('company_phone');
            $table->string('company_logo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['company_email', 'company_phone', 'fax_number', 'company_logo']);
        });
    }
};
