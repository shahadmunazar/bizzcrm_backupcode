<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onUpdate('cascade')->onDelete('restrict');
            $table->string('name');
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_modes');
    }
}
