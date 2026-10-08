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
        // 1. Refactor Phone Numbers
        if (Schema::hasTable('whatsapp_phone_numbers')) {
            Schema::rename('whatsapp_phone_numbers', 'whats_app_numbers');
            Schema::table('whats_app_numbers', function (Blueprint $table) {
                if (Schema::hasColumn('whats_app_numbers', 'whatsapp_business_account_id')) {
                    $table->renameColumn('whatsapp_business_account_id', 'target_id');
                }
                if (Schema::hasColumn('whats_app_numbers', 'phone_number_id')) {
                    $table->renameColumn('phone_number_id', 'whatsapp_id');
                }
                if (Schema::hasColumn('whats_app_numbers', 'verified_name')) {
                    $table->renameColumn('verified_name', 'name_status');
                }
                if (Schema::hasColumn('whats_app_numbers', 'code_verification_status')) {
                    $table->renameColumn('code_verification_status', 'verfification_code');
                }
            });
        }

        // 2. Refactor Portfolios to Templates
        if (Schema::hasTable('whatsapp_business_portfolios')) {
            Schema::rename('whatsapp_business_portfolios', 'whats_app_templates');
            Schema::table('whats_app_templates', function (Blueprint $table) {
                $table->dropColumn(['portfolio_id', 'timezone', 'currency', 'metadata']);
                $table->string('whats_app_id')->nullable()->after('box_id');
                $table->json('components')->nullable()->after('name');
                $table->string('language')->nullable()->after('components');
                $table->string('whats_app_status')->nullable()->after('language');
                $table->string('category')->nullable()->after('whats_app_status');
                $table->json('params')->nullable()->after('category');
                $table->string('status')->nullable()->after('params');
                $table->string('added_by')->nullable()->after('status');
                $table->string('last_updated_by')->nullable()->after('added_by');
            });
        }

        // 3. Rename Accounts
        if (Schema::hasTable('whatsapp_business_accounts')) {
            Schema::rename('whatsapp_business_accounts', 'whats_app_accounts');
        }

        // 4. Create Campaigns
        if (!Schema::hasTable('whatsapp_campaigns')) {
            Schema::create('whatsapp_campaigns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('box_id')->nullable();
                $table->unsignedBigInteger('whats_app_template_id')->nullable();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('name')->nullable();
                $table->timestamp('schedule_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->string('status')->nullable();
                $table->boolean('include_all')->default(false);
                $table->timestamp('send_at')->nullable();
                $table->json('params')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 5. Create Messages
        if (!Schema::hasTable('whatsapp_messages')) {
            Schema::create('whatsapp_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('box_id')->nullable();
                $table->unsignedBigInteger('whatsapp_campaign_id')->nullable();
                $table->unsignedBigInteger('lead_id')->nullable();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('message_id')->nullable();
                $table->string('conversation_id')->nullable();
                $table->timestamp('conversation_expiration')->nullable();
                $table->text('text')->nullable();
                $table->boolean('is_sent')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->boolean('is_delivered')->default(false);
                $table->timestamp('delivered_at')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->json('media')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic for safety
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_campaigns');
        
        if (Schema::hasTable('whats_app_accounts')) {
            Schema::rename('whats_app_accounts', 'whatsapp_business_accounts');
        }

        if (Schema::hasTable('whats_app_templates')) {
            Schema::rename('whats_app_templates', 'whatsapp_business_portfolios');
            // Note: Restoring dropped columns would require more detail here, 
            // but for a cleanup migration, dropIfExists is the standard reversal.
        }

        if (Schema::hasTable('whats_app_numbers')) {
            Schema::rename('whats_app_numbers', 'whatsapp_phone_numbers');
        }
    }
};
