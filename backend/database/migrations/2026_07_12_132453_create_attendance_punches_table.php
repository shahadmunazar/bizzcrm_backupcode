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
        Schema::create('attendance_punches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boxes_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('box_user_id')->nullable();
            $table->string('device_id');
            $table->string('action'); // punch_in, punch_out
            $table->timestamp('punched_at');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('signed_payload');
            $table->text('signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_punches');
    }
};
