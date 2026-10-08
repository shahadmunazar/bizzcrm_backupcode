<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('note');
            $table->json('files')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index(); // For nested comments
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
