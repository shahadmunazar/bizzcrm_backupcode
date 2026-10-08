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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->string('name', 55);
            $table->string('email')->nullable();
            $table->string('sec_email')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->text('query')->nullable();
            $table->string('designation')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 55)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('country_id')->default(101);
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->unsignedBigInteger('lead_status_id')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('drug_license')->nullable();
            $table->string('gstin')->nullable();
            $table->string('experience')->nullable();
            $table->string('location')->nullable();
            $table->string('created_from')->default('Manual');
            $table->unsignedBigInteger('last_activity_id')->nullable();
            $table->unsignedBigInteger('last_note_id')->nullable();
            $table->enum('is_matured', ['No', 'Yes'])->default('No');
            $table->date('matured_date')->nullable();
            $table->double('amount', 8, 2)->nullable();
            $table->enum('is_paid', ['0', '1'])->default('0');
            $table->string('lead_type')->default('Direct Lead');
            $table->dateTime('trashed_at')->nullable();
            $table->string('gstin_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('company_name')->nullable();

            // Foreign keys with RESTRICT on delete
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('restrict');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('restrict');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('lead_source_id')->references('id')->on('crm_sources')->onDelete('restrict');
            $table->foreign('lead_status_id')->references('id')->on('contact_stage')->onDelete('restrict');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('restrict');
            // $table->foreign('last_activity_id')->references('id')->on('activities')->onDelete('restrict');
            // $table->foreign('last_note_id')->references('id')->on('notes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
