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
             Schema::create('payroll_overtimes', function (Blueprint $table) {
                 $table->id();
                 $table->string('name')->nullable();
                 $table->string('rate_type')->nullable();
                 $table->decimal('rate', 10, 2)->default(0);
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
             Schema::table('payroll_overtimes', function (Blueprint $table) {
                 $table->dropForeign(['box_id']);
                 $table->dropForeign(['box_user_id']);
             });

             Schema::dropIfExists('payroll_overtimes');
         }
 };
