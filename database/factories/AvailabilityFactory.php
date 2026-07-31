<?php

namespace Database\Factories;

use App\Doctor\Models\Availability;
use App\Doctor\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Availability>
 */
class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('now', '+2 weeks');
        $endsAt = (clone $startsAt)->modify('+4 hours');

        return [
            'doctor_id' => Doctor::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'slot_duration' => 30,
        ];
    }
}
