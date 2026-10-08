<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CallLog>
 */
class CallLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['incoming', 'outgoing', 'missed'];
        $statuses = ['connected', 'no_answer'];
        $type = $this->faker->randomElement($types);
        $status = $type === 'missed' ? 'no_answer' : $this->faker->randomElement($statuses);
        
        $startTime = $this->faker->dateTimeBetween('-1 month', 'now');
        $duration = $status === 'connected' ? $this->faker->numberBetween(10, 1800) : 0;
        $endTime = (clone $startTime)->modify("+{$duration} seconds");

        return [
            'box_id' => 1,
            'created_by' => 1,
            'name' => $this->faker->name(),
            'from' => $this->faker->phoneNumber(),
            'to' => $this->faker->phoneNumber(),
            'type' => $type,
            'status' => $status,
            'duration' => $duration,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'rawTimestamp' => $startTime->getTimestamp(),
        ];
    }
}
