<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePharmaKhabarLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pharma_khabar_leads');
        Schema::create('pharma_khabar_leads', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('lead_api_box_id')->index();
            $table->foreign('lead_api_box_id')->references('id')->on('box_crm_integrations')->onDelete('cascade');

            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');

            $table->unsignedBigInteger('lead_id')->index()->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->foreign('lead_source_id')->references('id')->on('crm_sources')->onDelete('set null');
            $table->string('query_id')->index();
            $table->string('name')->nullable(); //sender_name
            $table->string('email')->nullable(); //sender_email
            $table->string('phone')->nullable(); //sender_mobile
            $table->string('company_name')->nullable(); //sender_company
            $table->string('address')->nullable(); //sender_address
            $table->string('city')->nullable(); //sender_city
            $table->string('state')->nullable(); //sender_state

            $table->string('source')->nullable(); //source
            $table->text('query')->nullable(); //query_message

            $table->dateTime('submit_time')->nullable(); //submit_time

            $table->enum('status', ['Pending', 'Imported', 'Skipped'])->default('Pending');

            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pharma_khabar_leads');
    }
}
