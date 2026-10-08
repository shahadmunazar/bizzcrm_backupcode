<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_generated_widgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->enum('type', ['link', 'button_widget'])->default('link');
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('custom_message')->nullable();
            $table->longText('config')->nullable(); // JSON configuration for buttons
            $table->longText('generated_url_or_code')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();

            // Setup foreign keys if needed. Ignoring constraints for now to prevent issues if box_id or added_by is missing/deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_generated_widgets');
    }
};
