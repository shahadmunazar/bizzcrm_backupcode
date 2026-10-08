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
        Schema::table('attendance_corrections', function (Blueprint $table) {
            // Add correction type column (attendance or break)
            if (!Schema::hasColumn('attendance_corrections', 'correction_type')) {
                $table->enum('correction_type', ['attendance', 'break'])->default('attendance')->after('attendance_id');
            }

            // Add break-related fields for break corrections
            if (!Schema::hasColumn('attendance_corrections', 'break_id')) {
                $table->unsignedBigInteger('break_id')->nullable()->after('correction_type');
            }

            if (!Schema::hasColumn('attendance_corrections', 'break_start')) {
                $table->datetime('break_start')->nullable()->after('break_id');
            }

            if (!Schema::hasColumn('attendance_corrections', 'break_end')) {
                $table->datetime('break_end')->nullable()->after('break_start');
            }

            // Add foreign key for break_id if it doesn't exist
            if (Schema::hasColumn('attendance_corrections', 'break_id')) {
                // Check if foreign key already exists
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys('attendance_corrections');
                
                $fkExists = false;
                foreach ($foreignKeys as $fk) {
                    if (in_array('break_id', $fk->getLocalColumns())) {
                        $fkExists = true;
                        break;
                    }
                }
                
                if (!$fkExists) {
                    $table->foreign('break_id')->references('id')->on('attendance_break')->onDelete('cascade');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_corrections', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('attendance_corrections', 'break_id')) {
                $table->dropForeign(['break_id']);
            }

            // Drop columns
            if (Schema::hasColumn('attendance_corrections', 'break_end')) {
                $table->dropColumn('break_end');
            }

            if (Schema::hasColumn('attendance_corrections', 'break_start')) {
                $table->dropColumn('break_start');
            }

            if (Schema::hasColumn('attendance_corrections', 'break_id')) {
                $table->dropColumn('break_id');
            }

            if (Schema::hasColumn('attendance_corrections', 'correction_type')) {
                $table->dropColumn('correction_type');
            }
        });
    }
};
