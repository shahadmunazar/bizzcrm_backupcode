<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTradeIndiaLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('trade_india_leads');
        Schema::create('trade_india_leads', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('lead_api_box_id')->index();
            $table->foreign('lead_api_box_id')->references('id')->on('box_crm_integrations')->onDelete('cascade');
            
            $table->unsignedBigInteger('box_id')->index();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');

            $table->unsignedBigInteger('lead_id')->index()->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->foreign('lead_source_id')->references('id')->on('crm_sources')->onDelete('set null');

            $table->string('rfi_id')->index(); //rfi_id from ApiFetch
            
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_mobile')->nullable();
            $table->string('sender_company')->nullable();
            $table->string('sender_address')->nullable();
            $table->string('sender_city')->nullable();
            $table->string('sender_state')->nullable();
            $table->string('sender_country')->nullable();
            
            $table->text('message')->nullable();
            $table->string('product_name')->nullable();
            
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
        Schema::dropIfExists('trade_india_leads');
    }
}
