<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('companies');
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // Using convenient alias for bigIncrements('id')

            // box_id - BigInteger to match boxes.id
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')
                  ->references('id')->on('boxes')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            
            $table->string('bill_name')->nullable();
//            $table->string('bill_email')->nullable();
            $table->string('bill_phone')->nullable();
            
            $table->string('pan')->nullable();
            $table->string('gstin')->nullable();


            $table->string('bill_address')->nullable();
            $table->integer('bill_pin_code')->nullable();

            // States and Cities usually BigInteger too
            $table->unsignedBigInteger('bill_state_id')->nullable();
            $table->foreign('bill_state_id')->references('id')->on('states')->onDelete('set null');

            $table->unsignedBigInteger('bill_city_id')->nullable();
            $table->foreign('bill_city_id')->references('id')->on('cities')->onDelete('set null');


            $table->enum('status', ['Enable', 'Disabled'])->default('Enable')->index();

            // created_by/added_by usually BigInteger
            $table->unsignedBigInteger('added_by')->index();
            $table->foreign('added_by')
                ->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('last_updated_by')->index();
            $table->foreign('last_updated_by')
                ->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('companies');
    }
}
