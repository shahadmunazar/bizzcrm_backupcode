<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('box_users', function (Blueprint $table) {
            $table->id();

            // Foreign Keys with deletion restriction
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('added_by')->constrained('users')->restrictOnDelete();

            // Email Config
            $table->enum('email_connected', ['0', '1'])->default('0');
            $table->enum('email_type', ['gmail', 'other'])->default('other');
            $table->string('email_incoming_user')->nullable();
            $table->string('email_incoming_password')->nullable();
            $table->string('email_outgoing_user')->nullable();
            $table->string('email_outgoing_password')->nullable();
            $table->enum('email_same_user', ['0', '1'])->default('1');
            $table->string('email_incoming')->nullable();
            $table->string('email_incoming_port')->default('993');
            $table->string('email_outgoing')->nullable();
            $table->string('email_outgoing_port')->default('465');
            $table->enum('email_allow_non_secure_certificate', ['0', '1'])->default('0');
            $table->string('encryption')->default('ssl');
            $table->string('email_token')->nullable();
            $table->string('email_refresh_token')->nullable();

            // Additional Info
            $table->text('allowed_ips')->nullable();
            $table->text('signature')->nullable();
            $table->enum('exlude_ip_restriction', ['0', '1'])->default('0');
            $table->enum('auto_responder', ['auto', 'manual'])->default('manual');
            $table->text('auto_responder_text')->nullable();
            $table->text('phone')->nullable();

            // Working Hours
            $table->string('working_days')->nullable();
            $table->string('day_start_time')->nullable();
            $table->string('day_end_time')->nullable();
            $table->dateTime('last_lead')->nullable();
            $table->enum('exclude_round_robin', ['0', '1'])->default('0');
            $table->enum('user_box_timing', ['0', '1'])->default('1');

            // Gmail App & Status
            $table->string('gmail_app')->default('first');
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled');

            // Auto Responder Settings
            $table->string('auto_responser_subject')->nullable();
            $table->string('error_log')->nullable();
            $table->text('auto_responder_attachments')->nullable();

            // Employee Info
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('designation')->nullable();
            $table->string('about')->nullable();
            $table->string('company')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('staff_role')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('department')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('assign_tour')->nullable();
            $table->string('assign_customer')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'custome'])->nullable();
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('profile_image_path')->nullable();
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

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('box_users');
    }
};
