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
        Schema::table('divisions', function (Blueprint $table) {
            if (!Schema::hasColumn('divisions', 'box_id')) {
                $table->unsignedBigInteger('box_id')->index()->after('id'); // ID cant be null
                $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            }

            if (!Schema::hasColumn('divisions', 'company_id')) {
                $table->unsignedBigInteger('company_id')->index()->after('box_id'); // ID cant be null
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            }

            if (!Schema::hasColumn('divisions', 'auto_map_loaction')) {
                $table->boolean('auto_map_loaction')->nullable()->after('name'); // Nullable as per request
            }

            if (!Schema::hasColumn('divisions', 'limit_member')) {
                $table->boolean('limit_member')->nullable()->after('auto_map_loaction'); // Nullable as per request
            }

            if (!Schema::hasColumn('divisions', 'max_members')) {
                $table->integer('max_members')->nullable()->after('limit_member');
            }

            if (!Schema::hasColumn('divisions', 'added_by')) {
                $table->unsignedBigInteger('added_by')->index()->after('max_members'); // ID cant be null
                $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('divisions', 'deleted_at')) {
                $table->softDeletes();
            }

            // Drop state_id if it exists (assuming it's replaced by the new structure)
            if (Schema::hasColumn('divisions', 'state_id')) {
                 $table->dropColumn('state_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['box_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['added_by']);
            
            $table->dropColumn([
                'box_id', 
                'company_id', 
                'auto_map_loaction', 
                'limit_member', 
                'max_members', 
                'added_by', 
                'deleted_at'
            ]);

            if (!Schema::hasColumn('divisions', 'state_id')) {
                $table->unsignedBigInteger('state_id')->nullable();
            }
        });
    }
};
