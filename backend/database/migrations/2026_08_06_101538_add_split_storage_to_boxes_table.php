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
            $table->bigInteger('local_storage_used_bytes')->default(0)->after('storage_used_bytes');
            $table->bigInteger('aws_storage_used_bytes')->default(0)->after('local_storage_used_bytes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            //
        });
    }
};
