<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateIndiaMartLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('india_mart_leads');
        Schema::create('india_mart_leads', function (Blueprint $table) {
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
            
            $table->string('name')->nullable(); //SENDER_NAME
            $table->string('email')->nullable(); //SENDER_EMAIL
            $table->string('sec_email')->nullable(); //SENDER_EMAIL_ALT
            $table->string('phone')->nullable(); //SENDER_MOBILE
            $table->string('phone_2')->nullable(); //SENDER_MOBILE_ALT
            $table->string('phone_type')->nullable(); //SENDER_PHONE
            $table->string('phone_type_2')->nullable(); //SENDER_PHONE_ALT
            $table->string('company_name')->nullable(); //SENDER_COMPANY
            $table->string('address')->nullable(); //SENDER_ADDRESS
            $table->string('city')->nullable(); //SENDER_CITY
            $table->string('state')->nullable(); //SENDER_STATE
            $table->string('country')->nullable(); //SENDER_COUNTRY_ISO
            $table->text('query')->nullable(); //QUERY_MESSAGE
            $table->string('mcenquiry_id')->nullable(); //MC_QUERY_ID
            $table->string('subject')->nullable(); //QUERY_SUBJECT
            $table->string('sender_glusr_id')->nullable(); //SENDER_GLUSR_ID
            $table->string('custtype_name')->nullable(); //CUSTTYPE_NAME
            $table->string('product_name')->nullable(); //PRODUCT_NAME
            
            $table->dateTime('date_time_re')->nullable(); //DATE_TIME_RE
            
            $table->enum('status', ['Pending', 'Imported','Skipped'])->default('Pending');

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
        Schema::dropIfExists('india_mart_leads');
    }
}
