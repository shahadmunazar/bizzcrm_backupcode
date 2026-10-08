<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
            $table->unsignedBigInteger('work_mode_id')->nullable()->after('shift_id');
            $table->json('working_web_days')->nullable()->after('work_mode_id');
        });
    }


    public function down(): void
    {
        Schema::table('box_users', function (Blueprint $table) {
                $table->dropColumn(['work_mode_id', 'working_days']);
        });
    }
};
