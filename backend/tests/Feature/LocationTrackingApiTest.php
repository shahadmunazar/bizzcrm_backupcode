<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Box;
use App\Models\UserCurrentLocation;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

class LocationTrackingApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $box;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->box = Box::create([
            'name' => 'Test Box',
            'slug' => 'test-box',
            'status' => 'Enabled'
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_submit_valid_location()
    {
        $response = $this->postJson("/{$this->box->slug}/api/location", [
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'accuracy' => 10.5,
            'timestamp' => Carbon::now()->toIso8601String()
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Location recorded successfully'
        ]);

        $this->assertDatabaseHas('user_current_locations', [
            'user_id' => $this->user->id,
            'box_id' => $this->box->id,
            'latitude' => 28.6139,
            'longitude' => 77.2090
        ]);

        $this->assertDatabaseHas('user_location_history', [
            'user_id' => $this->user->id,
            'latitude' => 28.6139,
            'longitude' => 77.2090
        ]);
    }

    public function test_rejects_invalid_coordinates()
    {
        $response = $this->postJson("/{$this->box->slug}/api/location", [
            'latitude' => 95.0000, // Invalid lat
            'longitude' => 190.0000, // Invalid lng
            'accuracy' => 10.5,
            'timestamp' => Carbon::now()->toIso8601String()
        ]);

        $response->assertStatus(422);
    }

    public function test_rejects_future_timestamps()
    {
        $response = $this->postJson("/{$this->box->slug}/api/location", [
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'accuracy' => 10.5,
            'timestamp' => Carbon::now()->addHour()->toIso8601String()
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Future timestamps are not allowed']);
    }

    public function test_rejects_low_accuracy()
    {
        config(['tracking.max_accuracy' => 50]);

        $response = $this->postJson("/{$this->box->slug}/api/location", [
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'accuracy' => 100, // Greater than max 50
            'timestamp' => Carbon::now()->toIso8601String()
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Location accuracy is too low and was discarded']);
    }

    public function test_prevents_duplicate_history_entries_for_close_pings()
    {
        // First ping
        $this->postJson("/{$this->box->slug}/api/location", [
            'latitude' => 28.61390000,
            'longitude' => 77.20900000,
            'accuracy' => 10,
            'timestamp' => Carbon::now()->toIso8601String()
        ]);

        // Second ping almost at the same spot 10 seconds later
        $this->postJson("/{$this->box->slug}/api/location", [
            'latitude' => 28.61390010,
            'longitude' => 77.20900010,
            'accuracy' => 10,
            'timestamp' => Carbon::now()->addSeconds(10)->toIso8601String()
        ]);

        // Should only have 1 history entry
        $this->assertEquals(1, \App\Models\UserLocationHistory::count());
        // Current location should be updated to the new one though
        $this->assertEquals(1, \App\Models\UserCurrentLocation::count());
    }

    public function test_can_fetch_current_locations()
    {
        UserCurrentLocation::create([
            'user_id' => $this->user->id,
            'box_id' => $this->box->id,
            'latitude' => 28.6139,
            'longitude' => 77.2090,
            'accuracy' => 10,
            'recorded_at' => Carbon::now()
        ]);

        $response = $this->getJson("/{$this->box->slug}/api/location/current");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['user_id', 'latitude', 'longitude']
            ]
        ]);
    }
}
