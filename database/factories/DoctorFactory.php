<?php

namespace Database\Factories;

use App\Doctor\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'specialty' => fake()->randomElement(['Cardiologist', 'Dentist', 'Dermatologist', 'Neurologist', 'Pediatrician', 'Psychiatrist']),
        ];
    }
}
