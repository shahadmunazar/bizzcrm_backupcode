<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\BoxUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BoxUserFactory extends Factory
{
    protected $model = BoxUser::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        
        return [
            'box_id' => Box::factory(),
            'user_id' => User::factory(),
            'added_by' => User::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => strtolower($firstName . '.' . $lastName),
            'phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->company(),
            'address' => $this->faker->address(),
            'employee_id' => strtoupper(Str::random(2)) . '-' . $this->faker->numberBetween(10, 99),
            'staff_role' => $this->faker->randomElement(['admin', 'manager', 'employee']),
            'status' => 'Enabled',
            'joining_date' => now()->format('Y-m-d'),
            'working_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'day_start_time' => '09:00',
            'day_end_time' => '18:00',
            'user_box_timing' => 1, // Use box timing
            'exlude_ip_restriction' => 0,
            'allowed_ips' => null,
        ];
    }

    public function forUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function forBox(Box $box): self
    {
        return $this->state(fn (array $attributes) => [
            'box_id' => $box->id,
        ]);
    }

    public function disabled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Disabled',
        ]);
    }

    public function withCustomTiming(): self
    {
        return $this->state(fn (array $attributes) => [
            'user_box_timing' => 0,
            'working_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'day_start_time' => '10:00',
            'day_end_time' => '19:00',
        ]);
    }

    public function withIpRestriction(array $allowedIps = ['192.168.1.1']): self
    {
        return $this->state(fn (array $attributes) => [
            'exlude_ip_restriction' => 1,
            'allowed_ips' => json_encode($allowedIps),
        ]);
    }
}

