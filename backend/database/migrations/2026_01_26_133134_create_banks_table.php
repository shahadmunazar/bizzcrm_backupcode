<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('banks');

        Schema::create('banks', function (Blueprint $table) {
            $table->id(); // Use bigIncrements

            // box_id
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onUpdate('cascade')->onDelete('restrict');

            // company_id
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->string('name');
            $table->string('account_no');
            $table->string('ifsc');
            $table->string('address'); // nullable? User just said string('address')
            
            $table->enum('show_pdf', ['No', 'Yes'])->default('No')->index();
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled')->index();

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
        Schema::dropIfExists('banks');
    }
}
