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
        Schema::create('bulk_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('box_id');
            $table->unsignedBigInteger('user_id')->comment('User who uploaded the file');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 10)->comment('csv, xlsx, xls');
            $table->bigInteger('file_size')->comment('File size in bytes');
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('skipped_rows')->default(0);
            $table->integer('duplicate_rows')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('mappings')->nullable()->comment('Field mappings used for import');
            $table->boolean('create_duplicate')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('box_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_uploads');
    }
};
