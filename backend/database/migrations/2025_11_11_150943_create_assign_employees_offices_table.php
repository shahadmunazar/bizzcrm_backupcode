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
        Schema::create('assign_employees_offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_user_id');
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('box_user_id')
                ->references('id')->on('box_users')
                ->onDelete('cascade');
            $table->foreign('branch_office_id')
                ->references('id')->on('company_branches')
                ->onDelete('cascade');
            $table->foreign('box_id')
                ->references('id')->on('boxes')
                ->onDelete('cascade');
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->nullOnDelete();
            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->nullOnDelete();
            $table->foreign('deleted_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_employees_offices');
    }
};
