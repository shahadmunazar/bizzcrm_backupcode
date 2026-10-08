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
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('punch_in_latitude', 10, 8)->nullable();
            $table->decimal('punch_in_longitude', 11, 8)->nullable();
            $table->string('punch_in_location')->nullable();
            
            $table->decimal('punch_out_latitude', 10, 8)->nullable();
            $table->decimal('punch_out_longitude', 11, 8)->nullable();
            $table->string('punch_out_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'punch_in_latitude', 
                'punch_in_longitude', 
                'punch_in_location', 
                'punch_out_latitude', 
                'punch_out_longitude', 
                'punch_out_location'
            ]);
        });
    }
};
