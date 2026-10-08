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
        Schema::create('mail_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->unsignedBigInteger('inbox_id')->index();
            $table->foreign('inbox_id')->references('id')->on('gmail_inboxes')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('from')->nullable();
            $table->string('subject')->nullable();
            $table->string('has_words')->nullable();
            $table->unsignedInteger('template_id')->index()->nullable();
            $table->foreign('template_id')->references('id')->on('lead_source_templates')->onDelete('cascade');
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            $table->unsignedBigInteger('assigned_to')->index()->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->foreign('lead_source_id')->references('id')->on('crm_sources')->onDelete('cascade');
            $table->unsignedBigInteger('lead_status_id')->nullable();
            $table->foreign('lead_status_id')->references('id')->on('contact_stage')->onDelete('cascade');
            $table->enum('round_robin', ['0', '1'])->default('0');
            $table->text('round_robin_users')->nullable();
            $table->unsignedBigInteger('added_by')->index();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('last_updated_by')->index();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_filters');
    }
};
