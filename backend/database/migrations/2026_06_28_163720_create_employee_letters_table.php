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
        Schema::create('employee_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('status')->default('Pending');
            $table->longText('content')->nullable();
            $table->string('requested_by')->nullable();
            $table->date('applied_date')->nullable();
            $table->date('release_date')->nullable();
            $table->string('letter_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_letters');
    }
};
