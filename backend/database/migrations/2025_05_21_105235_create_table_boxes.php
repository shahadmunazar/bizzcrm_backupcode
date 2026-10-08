<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();

            // Foreign Keys (modern style)
            $table->foreignId('superadmin_id')->constrained('users')->cascadeOnDelete();

            // Main fields
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique();
            $table->string('title')->nullable();
            $table->string('short_title')->nullable();
            $table->string('logo')->nullable();
            $table->text('ip_address')->nullable();
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled');
            $table->string('phone', 15)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('pincode')->nullable();
            $table->string('industry')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();

            // Reminder config
            $table->string('daily_reminder_time')->default('06:00');
            $table->integer('reminder_before')->default(5);
            $table->dateTime('last_reminder_checked')->nullable();
            $table->enum('lead_required_field', ['email', 'phone'])->default('email');

            // Working hours
            $table->string('working_days')->nullable();
            $table->string('day_start_time')->nullable();
            $table->string('day_end_time')->nullable();
            $table->enum('round_robin', ['0', '1'])->default('0');

            // SMS config
            $table->enum('sms_enable', ['No', 'Yes'])->default('No');
            $table->string('sms_key')->nullable();
            $table->string('sms_contacts')->nullable();
            $table->string('sms_senderid')->nullable();
            $table->enum('assigned_sms', ['No', 'Yes'])->default('No');
            $table->text('assigned_sms_text')->nullable();
            $table->text('round_robin_users')->nullable();
            $table->text('params')->nullable();

            // Email config
            $table->enum('can_use_admin_mail', ['0', '1']);
            $table->text('admin_mail_users')->nullable();

            // Date ranges
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Notifications
            $table->string('notification')->nullable();

            // WhatsApp
            $table->enum('whatsapp_enable', ['No', 'Yes'])->default('No');
            $table->string('whatsapp_business_id')->nullable();
            $table->string('whatsapp_phone_id')->nullable();
            $table->text('whatsapp_token')->nullable();

            // Auto Task Close
            $table->enum('auto_task_close', ['No', 'Yes'])->default('No');
            $table->integer('auto_task_close_days')->nullable();

            // Other
            $table->longText('facebook_api_params')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
