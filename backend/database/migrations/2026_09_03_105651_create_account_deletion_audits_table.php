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
        Schema::create('account_deletion_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_deletion_request_id')->constrained('account_deletion_requests')->onDelete('cascade');
            $table->string('event');
            $table->text('description');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_deletion_audits');
    }
};
