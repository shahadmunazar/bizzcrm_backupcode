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
        Schema::create('account_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->string('reason');
            $table->string('experience')->nullable();
            $table->json('problems')->nullable();
            $table->text('feedback')->nullable();
            $table->json('confirmations')->nullable();
            $table->string('otp_hash')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->enum('status', [
                'pending_verification', 
                'pending_review', 
                'approved', 
                'rejected', 
                'processing', 
                'completed', 
                'cancelled'
            ])->default('pending_verification');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('scheduled_deletion_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_deletion_requests');
    }
};
