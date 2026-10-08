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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('box_user_id')->nullable();
            $table->string('leave_type')->nullable();
            $table->string('leave_balance')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('total_days')->nullable(); // duration
            $table->string('contact_during_leave')->nullable(); // contact info
            $table->text('reason')->nullable(); // leave reason
            $table->enum('status', ['pending', 'approved', 'hold','rejected'])->default('pending'); // approval status
            $table->unsignedBigInteger('created_by')->nullable(); // admin/user who created
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Foreign key constraints (optional but recommended)
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('set null');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
