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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('tags')->nullable()->after('company_name');
            $table->text('description')->nullable()->after('tags');
            $table->string('visibility')->nullable()->after('description');
            $table->string('status')->nullable()->after('visibility');
            $table->string('last_name')->nullable()->after('name');
            $table->string('currency', 10)->nullable()->after('amount');
            $table->string('phone_type')->nullable()->after('phone');
            $table->string('industry')->nullable()->after('phone_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['tags', 'description', 'visibility', 'status', 'last_name', 'currency', 'phone_type', 'industry']);
        });
    }
};
