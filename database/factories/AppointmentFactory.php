<?php

namespace Database\Factories;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Appointment\Models\Appointment;
use App\Doctor\Models\Doctor;
use App\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(AppointmentStatusEnum::cases());
        $startTime = fake()->dateTimeBetween('now', '+2 weeks');
        $endTime = (clone $startTime)->modify('+30 minutes');

        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
            'cancellation_reason' => $status === AppointmentStatusEnum::CANCELLED ? fake()->sentence() : null,
        ];
    }
}
