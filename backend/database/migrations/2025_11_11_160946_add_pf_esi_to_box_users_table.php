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
        Schema::table('box_users', function (Blueprint $table) {
            $table->string('pf_no')->nullable()->after('phone');
            $table->string('esi_no')->nullable()->after('pf_no');
            $table->date('resignation_date')->nullable()->after('esi_no'); // fixed typo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
            $table->dropColumn(['pf_no', 'esi_no', 'resignation_date']); 
        });
    }
};
