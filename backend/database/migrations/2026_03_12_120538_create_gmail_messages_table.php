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
        Schema::create('gmail_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gmail_inbox_id');
            $table->string('message_id')->unique();
            $table->string('thread_id');
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('subject')->nullable();
            $table->text('snippet')->nullable();
            $table->longText('body')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->foreign('gmail_inbox_id')->references('id')->on('gmail_inboxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gmail_messages');
    }
};
