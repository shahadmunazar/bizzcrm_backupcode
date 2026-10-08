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
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('reply_to_message_id');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            $table->timestamp('pin_expires_at')->nullable()->after('pinned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'pinned_at', 'pin_expires_at']);
        });
    }
};
