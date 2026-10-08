<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id(); // UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // Foreign key to countries table
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();

            $table->string('name', 200);
            $table->enum('status', ['Enable', 'Disabled'])->default('Enable');
            $table->string('code', 100)->nullable();
            $table->timestamps();

            // Optional: Index for search on name
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
