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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('discount_badge')->nullable(); // e.g. "30% Off"
            $table->string('savings_text')->nullable(); // e.g. "(Save ₹605)"
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('billing_cycle')->nullable();
            $table->string('marketing_msg_cost')->nullable();
            $table->string('utility_msg_cost')->nullable();
            $table->string('button_text')->default('Get Offer Now');
            $table->boolean('is_highlighted')->default(false); // true for Pro plan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'discount_badge',
                'savings_text',
                'original_price',
                'billing_cycle',
                'marketing_msg_cost',
                'utility_msg_cost',
                'button_text',
                'is_highlighted',
            ]);
        });
    }
};
