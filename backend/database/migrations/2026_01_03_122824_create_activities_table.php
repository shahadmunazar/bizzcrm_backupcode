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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->integer('activity_id');
            $table->string('activity_type')->nullable();
            $table->string('label')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['note', 'task', 'email', 'call', 'meeting', 'query', 'history', 'sms', 'general', 'pdf', 'receipt'])->default('general');
            $table->string('task_type')->nullable();
            $table->dateTime('activity_date');
            $table->dateTime('reminder_date')->nullable();
            $table->text('params');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('added_by');
            $table->unsignedBigInteger('last_updated_by');
            $table->enum('status', ['Open', 'Close', 'Failed'])->default('Open');
            $table->dateTime('completed_date')->nullable();
            $table->enum('email_sent', ['0', '1', '2'])->default('0');
            $table->unsignedBigInteger('activity_status_id')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->string('activity_type_status')->nullable();
            $table->text('error_log')->nullable();
            $table->string('created_from')->nullable();
            $table->integer('done_with')->nullable();
            $table->string('email_sent_through')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('activity_status_id')->references('id')->on('activity_statuses')->onDelete('set null');

            // Indexes
            $table->index('box_id');
            $table->index('activity_id');
            $table->index('activity_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
