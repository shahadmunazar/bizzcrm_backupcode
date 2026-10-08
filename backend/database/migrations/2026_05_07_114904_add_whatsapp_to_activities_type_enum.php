<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enums in MySQL are tricky to update via Blueprint in old Laravel versions or specific DBs.
        // Direct SQL is most reliable for changing ENUM definitions.
        DB::statement("ALTER TABLE activities MODIFY COLUMN type ENUM('note','task','email','call','meeting','query','history','sms','general','pdf','receipt', 'whatsapp') NOT NULL DEFAULT 'general'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE activities MODIFY COLUMN type ENUM('note','task','email','call','meeting','query','history','sms','general','pdf','receipt') NOT NULL DEFAULT 'general'");
    }
};
