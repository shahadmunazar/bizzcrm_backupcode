<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id(); // UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('code', 5);
            $table->integer('dialing_code')->nullable();
            $table->string('name', 200);
            $table->enum('status', ['Enable', 'Disabled'])->default('Enable');
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
