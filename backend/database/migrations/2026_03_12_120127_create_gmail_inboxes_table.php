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
        Schema::create('gmail_inboxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->unique();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->integer('expires_in')->nullable();
            $table->string('token_type')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gmail_inboxes');
    }
};
