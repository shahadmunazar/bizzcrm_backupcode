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
        Schema::create('facebook_accounts', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('box_id')->constrained('boxes')->onDelete('cascade');
            $blueprint->string('name')->nullable();
            $blueprint->string('facebook_id')->nullable();
            $blueprint->text('access_token');
            $blueprint->timestamp('expires_at')->nullable();
            $blueprint->string('status')->default('Enabled');
            $blueprint->text('api_params')->nullable();
            $blueprint->timestamps();
        });

        // Add facebook_account_id to facebook_pages
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->foreignId('facebook_account_id')->nullable()->after('box_id')->constrained('facebook_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facebook_pages', function (Blueprint $table) {
            $table->dropForeign(['facebook_account_id']);
            $table->dropColumn('facebook_account_id');
        });
        Schema::dropIfExists('facebook_accounts');
    }
};
