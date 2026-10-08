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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable();
            }
            if (!Schema::hasColumn('users', 'oauth_provider_id')) {
                $table->string('oauth_provider_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'email_verified')) {
                $table->enum('email_verified', ['1', '0'])->default('0');
            }
            if (!Schema::hasColumn('users', 'email_verification_code')) {
                $table->string('email_verification_code', 60)->nullable();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['Enabled', 'Disabled', 'Pending'])->default('Enabled');
            }
            if (!Schema::hasColumn('users', 'android_device_id')) {
                $table->string('android_device_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'ios_device_id')) {
                $table->string('ios_device_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->dateTime('last_login')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'active_status')) {
                $table->enum('active_status', ['active', 'ideal', 'inactive'])->default('inactive');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'oauth_provider',
                'oauth_provider_id',
                'avatar',
                'email_verified',
                'email_verification_code',
                'status',
                'android_device_id',
                'ios_device_id',
                'last_login',
                'last_login_ip',
                'active_status'
            ]);
        });
    }
};
