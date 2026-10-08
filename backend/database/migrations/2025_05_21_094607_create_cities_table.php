<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id(); // UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // Foreign key to states table
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();

            $table->string('name', 200);
            $table->enum('status', ['Enable', 'Disabled'])->default('Enable');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->timestamps();

            // Optional: Add index to name if needed for search performance
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
