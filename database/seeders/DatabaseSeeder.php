<?php

namespace Database\Seeders;

use App\Appointment\Models\Appointment;
use App\Doctor\Models\Availability;
use App\Doctor\Models\Doctor;
use App\Patient\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $doctors = Doctor::factory(5)->create();
        $patients = Patient::factory(20)->create();

        $doctors->each(function ($doctor) {
            for ($i = 1; $i <= 5; $i++) {
                $date = Carbon::now()->addDays($i)->setTime(8, 0, 0);
                Availability::factory()->create([
                    'doctor_id' => $doctor->id,
                    'starts_at' => $date,
                    'ends_at' => (clone $date)->addHours(8),
                    'slot_duration' => 30,
                ]);
            }
        });

        for ($i = 0; $i < 30; $i++) {
            $doctor = $doctors->random();
            $patient = $patients->random();

            // To ensure we don't try to get a random from an empty collection if something goes wrong, though we just created them.
            if ($doctor->availabilities()->count() > 0) {
                $availability = $doctor->availabilities()->inRandomOrder()->first();

                $slots = $availability->ends_at->diffInMinutes($availability->starts_at) / 30;
                $randomSlot = rand(0, $slots - 1);
                $startTime = clone $availability->starts_at;
                $startTime->addMinutes($randomSlot * 30);
                $endTime = (clone $startTime)->addMinutes(30);

                // Prevent double booking in seeder
                $doctorBooked = Appointment::where('doctor_id', $doctor->id)->where('start_time', '<', $endTime)->where('end_time', '>', $startTime)->exists();
                $patientBooked = Appointment::where('patient_id', $patient->id)->where('start_time', '<', $endTime)->where('end_time', '>', $startTime)->exists();

                if (! $doctorBooked && ! $patientBooked) {
                    Appointment::factory()->create([
                        'doctor_id' => $doctor->id,
                        'patient_id' => $patient->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                }
            }
        }
    }
}
