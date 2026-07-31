<?php

namespace Tests\Feature;

use App\Appointment\Models\Appointment;
use App\Doctor\Models\Availability;
use App\Doctor\Models\Doctor;
use App\Patient\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    private Doctor $doctor;

    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = Doctor::create(['name' => 'Dr. House', 'email' => 'house@example.com', 'specialty' => 'Diagnostician']);
        $this->patient = Patient::create(['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '123456']);

        Availability::create([
            'doctor_id' => $this->doctor->id,
            'starts_at' => now()->addDays(2)->setTime(9, 0),
            'ends_at' => now()->addDays(2)->setTime(12, 0),
            'slot_duration' => 30,
        ]);
    }

    public function test_can_list_free_slots()
    {
        $response = $this->getJson(route('v1.free-slots.getFreeSlots', [
            'doctor_id' => $this->doctor->id,
            'from' => now()->addDays(2)->setTime(8, 0)->toDateTimeString(),
            'to' => now()->addDays(2)->setTime(13, 0)->toDateTimeString(),
        ]));

        $response->assertStatus(200);
        $this->assertCount(6, $response->json('data')); // 3 hours / 30 mins = 6 slots
    }

    public function test_can_book_appointment()
    {
        $startTime = now()->addDays(2)->setTime(9, 0)->toDateTimeString();

        $response = $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_book_unavailable_slot()
    {
        // Not in availability
        $startTime = now()->addDays(2)->setTime(14, 0)->toDateTimeString();

        $response = $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
        ]);

        $response->assertStatus(422); // SlotNotAvailableException is thrown, but in controller it might throw 500 without handler.
        // Wait, Laravel auto-renders custom exceptions? If we set status code in exception... Yes, I set it to 422!
    }

    public function test_cannot_double_book_doctor()
    {
        $startTime = now()->addDays(2)->setTime(9, 30)->toDateTimeString();

        // First booking
        $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
        ])->assertStatus(201);

        // Second booking same time, different patient
        $patient2 = Patient::create(['name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '1234567']);

        $response = $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $patient2->id,
            'start_time' => $startTime,
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_double_book_patient()
    {
        $startTime = now()->addDays(2)->setTime(10, 0)->toDateTimeString();

        // First booking
        $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
        ])->assertStatus(201);

        // Second booking same time, different doctor
        $doctor2 = Doctor::create(['name' => 'Dr. Wilson', 'email' => 'wilson@example.com', 'specialty' => 'Oncologist']);
        Availability::create([
            'doctor_id' => $doctor2->id,
            'starts_at' => now()->addDays(2)->setTime(9, 0),
            'ends_at' => now()->addDays(2)->setTime(12, 0),
        ]);

        $response = $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $doctor2->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
        ]);

        $response->assertStatus(422);
    }

    public function test_can_change_status()
    {
        $startTime = now()->addDays(2)->setTime(10, 30)->toDateTimeString();

        $appointmentResponse = $this->postJson(route('v1.appointments.bookAppointment'), [
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
        ]);

        $appointmentId = $appointmentResponse->json('data.id');

        $response = $this->patchJson(route('v1.appointments.updateAppointmentStatus', ['appointment' => $appointmentId]), [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('confirmed', $response->json('data.status'));
    }

    public function test_cannot_cancel_confirmed_too_late()
    {
        // Start time is in 1 hour
        $startTime = now()->addHours(1)->toDateTimeString();
        Availability::create([
            'doctor_id' => $this->doctor->id,
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
        ]);

        $appointment = Appointment::create([
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'start_time' => $startTime,
            'end_time' => now()->addHours(1)->addMinutes(30)->toDateTimeString(),
            'status' => 'confirmed',
        ]);

        $response = $this->patchJson(route('v1.appointments.updateAppointmentStatus', ['appointment' => $appointment->id]), [
            'status' => 'cancelled',
            'cancellation_reason' => 'Too late',
        ]);

        $response->assertStatus(422); // CancellationTooLateException
    }
}
