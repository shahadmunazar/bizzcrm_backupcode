<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_user_id')->constrained('box_users')->restrictOnDelete();
            $table->foreignId('box_id')->constrained('boxes')->restrictOnDelete();

            // Auditable fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->date('attendance_date');
            $table->string('status')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->time('break_duration')->nullable()->comment('Break duration in minutes');
            $table->time('late_duration')->nullable()->comment('Late duration in minutes');
            $table->decimal('production_hours', 4, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
