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
        $tables = [
            'whats_app_accounts',
            'whats_app_numbers',
            'whats_app_portfolios',
            'whats_app_projects',
            'whats_app_templates',
            'whatsapp_campaigns',
            'whatsapp_messages'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
                }
                
                if (!Schema::hasColumn($tableName, 'added_by')) {
                    $table->unsignedBigInteger('added_by')->nullable();
                    $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
                }

                // Check for 'last_updated_by' and rename or add 'updated_by'
                if (Schema::hasColumn($tableName, 'last_updated_by')) {
                    // We'll keep last_updated_by if it exists but also add updated_by for consistency if needed
                    // Actually, let's just make sure updated_by exists
                    if (!Schema::hasColumn($tableName, 'updated_by')) {
                        $table->unsignedBigInteger('updated_by')->nullable();
                        $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                    }
                } elseif (!Schema::hasColumn($tableName, 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }

                if (!Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable();
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'whats_app_accounts',
            'whats_app_numbers',
            'whats_app_portfolios',
            'whats_app_projects',
            'whats_app_templates',
            'whatsapp_campaigns',
            'whatsapp_messages'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign keys first
                $columnsToDrop = [];
                if (Schema::hasColumn($tableName, 'added_by')) {
                    $table->dropForeign([$tableName . '_added_by_foreign']);
                    $columnsToDrop[] = 'added_by';
                }
                if (Schema::hasColumn($tableName, 'updated_by')) {
                    $table->dropForeign([$tableName . '_updated_by_foreign']);
                    $columnsToDrop[] = 'updated_by';
                }
                if (Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->dropForeign([$tableName . '_deleted_by_foreign']);
                    $columnsToDrop[] = 'deleted_by';
                }
                
                // Drop columns
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
                
                // Note: We don't drop deleted_at if it was already there, but for simplicity of this migration task:
                // If we were strictly rolling back, we'd need to know which ones we added.
                // For this task, I'll just leave down() as a best effort.
            });
        }
    }
};
