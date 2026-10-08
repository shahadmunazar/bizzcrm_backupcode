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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('box_id')->nullable();
            $table->string('name')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('type')->nullable(); // incoming, outgoing, missed
            $table->string('status')->nullable(); // connected, no_answer
            $table->integer('duration')->default(0); // in seconds
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('rawTimestamp')->nullable();
            $table->decimal('start_lat', 10, 7)->nullable();
            $table->decimal('start_long', 10, 7)->nullable();
            $table->decimal('end_lat', 10, 7)->nullable();
            $table->decimal('end_long', 10, 7)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
