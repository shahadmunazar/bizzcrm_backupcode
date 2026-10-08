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
        // Update division_areas
        if (Schema::hasTable('division_areas')) {
            Schema::table('division_areas', function (Blueprint $table) {
                // Add new columns if they don't exist
                if (!Schema::hasColumn('division_areas', 'state_id')) {
                    $table->unsignedBigInteger('state_id')->nullable()->after('division_id');
                    $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
                }
                
                if (!Schema::hasColumn('division_areas', 'city_id')) {
                    $table->unsignedBigInteger('city_id')->nullable()->after('state_id');
                    $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
                }

                if (!Schema::hasColumn('division_areas', 'lead_id')) {
                    $table->unsignedBigInteger('lead_id')->nullable()->after('city_id');
                    // $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
                }

                if (!Schema::hasColumn('division_areas', 'lat')) {
                    $table->decimal('lat', 10, 8)->nullable()->after('lead_id');
                }
                if (!Schema::hasColumn('division_areas', 'lng')) {
                    $table->decimal('lng', 11, 8)->nullable()->after('lat');
                }
                if (!Schema::hasColumn('division_areas', 'radius')) {
                    $table->double('radius')->nullable()->after('lng');
                }

                if (!Schema::hasColumn('division_areas', 'limit_member')) {
                    $table->boolean('limit_member')->nullable()->after('city_id');
                }

                if (!Schema::hasColumn('division_areas', 'max_members')) {
                    $table->integer('max_members')->nullable()->after('limit_member');
                }
                
                // Existing migration had 'name', user snippet doesn't show it but good to keep or ignore.
                // Existing migration had 'status', user snippet doesn't show it.
            });
        } else {
             // If for some reason checking table fails or it doesn't exist (e.g. fresh install vs partial)
             Schema::create('division_areas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('box_id')->index(); // ID cant be null
                $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
                
                $table->unsignedBigInteger('division_id'); // ID cant be null
                
                $table->unsignedBigInteger('state_id')->nullable();
                $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');

                $table->unsignedBigInteger('city_id')->nullable();
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');

                $table->unsignedBigInteger('lead_id')->nullable();
                $table->decimal('lat', 10, 8)->nullable();
                $table->decimal('lng', 11, 8)->nullable();
                $table->double('radius')->nullable();

                $table->boolean('limit_member')->nullable();
                $table->integer('max_members')->nullable();
                $table->timestamps();
            });
        }
        
        // Update area_leads
        // The existing migration 2026_01_29_120000_create_area_leads_table.php actually seems to cover MOST of what user asked,
        // specifically division_id, division_area_id, area_lead_id are there.
        // User snippet has fewer columns than existing. 
        // Existing has created_by, updated_by, lat, lng (added in 121500).
        // I should ensuring the types match.
        // existing has unsignedBigInteger for ids, user requested increments(id) and integer(..)->unsigned(). Identical.
        
        // No specific changes needed for area_leads based on comparison, 
        // existing table is actually richer.
        // I will assume the user just wants to ensure the KEY columns are present.
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('division_areas', function (Blueprint $table) {
             $table->dropForeign(['state_id']);
             $table->dropForeign(['city_id']);
             $table->dropColumn(['state_id', 'city_id', 'limit_member', 'max_members']);
        });
    }
};
