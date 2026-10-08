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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('box_user_id')->nullable();
            $table->string('title');
            $table->string('category')->nullable();
            $table->json('responsible_persons')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('tags')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('box_user_id')->references('id')->on('box_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
