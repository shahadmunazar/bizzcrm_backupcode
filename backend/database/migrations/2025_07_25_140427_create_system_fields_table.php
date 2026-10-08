<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemFieldsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->string('name');
            $table->string('object');
            $table->string('field');
            $table->string('field_name');
            $table->string('field_type');
            $table->string('field_refernce')->nullable();
            $table->string('field_refernce_key')->nullable();
            $table->json('options')->nullable();
            $table->string('validation_rules')->nullable();
            $table->integer('sequence')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->boolean('is_sortable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->string('placeholder')->nullable();
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_fields');
    }
}
