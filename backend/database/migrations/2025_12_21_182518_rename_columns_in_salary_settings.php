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
        Schema::table('salary_settings', function (Blueprint $table) {
            if (Schema::hasColumn('salary_settings', 'name') && !Schema::hasColumn('salary_settings', 'frequency')) {
                $table->renameColumn('name', 'frequency');
            }

            if (Schema::hasColumn('salary_settings', 'amount') && !Schema::hasColumn('salary_settings', 'salary_structure_name')) {
                $table->string('amount', 255)->change();
                $table->renameColumn('amount', 'salary_structure_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            if (Schema::hasColumn('salary_settings', 'frequency')) {
                $table->renameColumn('frequency', 'name');
            }

            if (Schema::hasColumn('salary_settings', 'salary_structure_name')) {
                $table->renameColumn('salary_structure_name', 'amount');
                $table->decimal('amount', 12, 2)->default(0)->change();
            }
        });
    }
};
