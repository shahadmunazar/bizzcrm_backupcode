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
            'letter_types',
            'letter_signatories',
            'letter_heads',
            'letter_templates',
            'employee_letters'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if ($tableName !== 'employee_letters' && !Schema::hasColumn($tableName, 'status')) {
                    $table->enum('status', ['Active', 'Inactive'])->default('Active');
                }
                if (!Schema::hasColumn($tableName, 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
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
            'letter_types',
            'letter_signatories',
            'letter_heads',
            'letter_templates',
            'employee_letters'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = ['created_by', 'updated_by', 'deleted_by', 'deleted_at'];
                if ($tableName !== 'employee_letters') {
                    $columns[] = 'status';
                }
                $table->dropColumn($columns);
            });
        }
    }
};
