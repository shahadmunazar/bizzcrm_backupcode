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
        if (!Schema::hasTable('vendor_referral_invitations')) {
            Schema::create('vendor_referral_invitations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->string('recipient_email')->index();
                $table->string('referral_code')->nullable();
                $table->string('referral_url', 500)->nullable();
                $table->text('message')->nullable();
                $table->string('status', 50)->default('pending')->index(); // 'pending', 'joined'
                $table->unsignedBigInteger('registered_vendor_id')->nullable()->index();
                $table->timestamp('registered_at')->nullable();
                $table->timestamp('last_sent_at')->nullable();
                $table->timestamps();

                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
                $table->foreign('registered_vendor_id')->references('id')->on('vendors')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_referral_invitations');
    }
};
