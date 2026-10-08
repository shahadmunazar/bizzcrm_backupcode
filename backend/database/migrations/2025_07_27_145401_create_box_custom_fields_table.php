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
        Schema::create('box_custom_fields', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('box_id');
            $table->string('type');
            $table->string('col_name');
            $table->string('name');
            $table->text('error_message')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('readonly')->nullable();
            $table->boolean('hidden')->nullable();
            $table->json('params')->nullable();
            $table->string('object')->nullable();
            $table->integer('ordering')->default(0);
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled');
            $table->boolean('quick_edit')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('last_updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_custom_fields');
    }
};
