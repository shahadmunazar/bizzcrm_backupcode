<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BoxFactory extends Factory
{
    protected $model = Box::class;

    public function definition(): array
    {
        $companyName = $this->faker->company();
        
        return [
            'superadmin_id' => User::factory(),
            'name' => $companyName,
            'slug' => Str::slug($companyName),
            'domain' => Str::slug($companyName) . '.mycrm.com',
            'industry' => $this->faker->randomElement([
                'Technology',
                'Banking',
                'Healthcare',
                'Retail',
                'Insurance'
            ]),
            'address' => $this->faker->address(),
            'pincode' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'mobile' => $this->faker->phoneNumber(),
            'contact_name' => $this->faker->name(),
            'country_id' => $this->faker->numberBetween(1, 100),
            'state_id' => $this->faker->numberBetween(1, 50),
            'city_id' => $this->faker->numberBetween(1, 100),
            'start_date' => now(),
            'working_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'day_start_time' => '09:00',
            'day_end_time' => '18:00',
            'status' => 'Active',
        ];
    }

    public function withSuperadmin(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'superadmin_id' => $user->id,
        ]);
    }

    public function disabled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Disabled',
        ]);
    }
}

