<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('pincode', 10);
            $table->text('address');
            $table->foreignId('country_id')->constrained('countries')->restrictOnDelete();
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
