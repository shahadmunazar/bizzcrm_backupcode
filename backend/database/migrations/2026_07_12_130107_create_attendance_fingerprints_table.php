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
        Schema::create('attendance_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boxes_id');
            $table->unsignedBigInteger('user_id');
            $table->string('device_id');
            $table->text('public_key');
            $table->string('key_algorithm')->nullable();
            $table->string('status')->default('active'); // active, revoked
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_fingerprints');
    }
};
