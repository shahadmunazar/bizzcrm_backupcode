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
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('is_unit_calculation')->default(false);
            $table->enum('assignee_type', ['no_assignee', 'all_employees', 'select_employee'])
                ->default('no_assignee');
            $table->unsignedBigInteger('box_id')->nullable()->index();
            $table->unsignedBigInteger('box_user_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('box_id')
                ->references('id')
                ->on('boxes')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->foreign('box_user_id')
                ->references('id')
                ->on('box_users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_deductions', function (Blueprint $table) {
            // Drop foreign keys before dropping the table
            $table->dropForeign(['box_id']);
            $table->dropForeign(['box_user_id']);
        });

        Schema::dropIfExists('payroll_deductions');
    }
};
