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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('designation')->nullable();
            $table->string('about')->nullable();
            $table->string('company')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->date('joining_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('department')->nullable();
            $table->string('password')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('assign_tour')->nullable();
            $table->string('staff_role')->nullable();
            $table->string('assign_customer')->nullable();
            $table->string('profile_image_path')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'custome'])->nullable();
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->string('passport_no')->nullable();
            $table->date('passport_exp_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->enum('marital_status', [
                'single',
                'married',
                'divorced',
                'widowed',
                'separated',
                'in_a_relationship',
            ])->nullable();
            $table->string('Employment_of_spouse')->nullable();
            $table->string('no_of_children')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
