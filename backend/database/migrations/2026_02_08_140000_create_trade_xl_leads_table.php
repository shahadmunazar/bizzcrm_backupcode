<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTradeXlLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('trade_xl_leads');
        Schema::create('trade_xl_leads', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('lead_api_box_id')->index(); 
            $table->foreign('lead_api_box_id')->references('id')->on('box_crm_integrations')->onDelete('cascade');
            
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');

            $table->unsignedBigInteger('lead_id')->index()->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->foreign('lead_source_id')->references('id')->on('crm_sources')->onDelete('set null');

            $table->string('enquiry_id')->index(); 
            
            $table->string('contact_person_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email_id')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('telephone_no')->nullable();
            $table->text('city_state')->nullable();
            $table->string('subject')->nullable();
            $table->string('product')->nullable(); 
            $table->string('message')->nullable(); 
            $table->dateTime('submit_time')->nullable();
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
        Schema::dropIfExists('trade_xl_leads');
    }
}
