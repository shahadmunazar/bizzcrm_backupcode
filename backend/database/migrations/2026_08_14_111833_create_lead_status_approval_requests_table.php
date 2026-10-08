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
        Schema::create('lead_status_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('old_status_id')->nullable();
            $table->unsignedBigInteger('new_status_id')->nullable();
            $table->unsignedBigInteger('old_sub_status_id')->nullable();
            $table->unsignedBigInteger('new_sub_status_id')->nullable();
            $table->unsignedBigInteger('approval_designation_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'disapproved'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            // Foreign keys
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('restrict');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_status_approval_requests');
    }
};
