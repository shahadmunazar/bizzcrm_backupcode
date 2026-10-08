<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePharmaVendLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pharma_vend_leads');
        Schema::create('pharma_vend_leads', function (Blueprint $table) {
            
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
            $table->string('name')->nullable();
            $table->string('companyname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->text('location')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->text('message')->nullable(); 
            $table->string('gst_number')->nullable();
            $table->string('drug_license')->nullable();
            $table->text('requirment_type')->nullable();
            $table->string('call_time')->nullable();
            $table->string('profession')->nullable();
            $table->string('experience')->nullable();
            $table->string('type')->nullable();
            
            $table->dateTime('submit_time')->nullable();
             
            $table->enum('status', ['Pending', 'Imported','Skipped','Updated'])->default('Pending');

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
        Schema::dropIfExists('pharma_vend_leads');
    }
}
