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
        Schema::create('user_current_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('boxuser_id')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable();
            
            // Device Status
            $table->tinyInteger('battery_level')->nullable(); // 0-100
            $table->boolean('is_charging')->nullable();
            $table->string('network_status', 50)->nullable(); // e.g. online, offline
            $table->string('network_type', 50)->nullable(); // e.g. wifi, 4g, 5g
            $table->timestamp('last_online_at')->nullable();
            $table->timestamp('last_offline_at')->nullable();
            
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('recorded_at')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('box_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_current_locations');
    }
};
