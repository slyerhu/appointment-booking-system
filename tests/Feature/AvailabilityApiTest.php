<?php

namespace Tests\Feature;

use App\Doctor\Models\Availability;
use App\Doctor\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityApiTest extends TestCase
{
    use RefreshDatabase;

    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = Doctor::create([
            'name' => 'Dr. Emmett Brown',
            'email' => 'doc.brown@example.com',
            'specialty' => 'Time Travel',
        ]);
    }

    public function test_can_create_availability()
    {
        $response = $this->postJson(route('v1.doctors.availabilities.createAvailabilityForDoctor', ['doctor' => $this->doctor->id]), [
            'starts_at' => now()->addDays(1)->setHour(9)->setMinute(0)->toDateTimeString(),
            'ends_at' => now()->addDays(1)->setHour(17)->setMinute(0)->toDateTimeString(),
            'slot_duration' => 30,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'slot_duration' => 30,
            ]);

        $this->assertDatabaseHas('availabilities', ['doctor_id' => $this->doctor->id, 'slot_duration' => 30]);
    }

    public function test_cannot_create_overlapping_availability()
    {
        Availability::create([
            'doctor_id' => $this->doctor->id,
            'starts_at' => now()->addDays(2)->setHour(10)->setMinute(0),
            'ends_at' => now()->addDays(2)->setHour(12)->setMinute(0),
            'slot_duration' => 30,
        ]);

        $response = $this->postJson(route('v1.doctors.availabilities.createAvailabilityForDoctor', ['doctor' => $this->doctor->id]), [
            'starts_at' => now()->addDays(2)->setHour(11)->setMinute(0)->toDateTimeString(),
            'ends_at' => now()->addDays(2)->setHour(13)->setMinute(0)->toDateTimeString(),
            'slot_duration' => 30,
        ]);

        // OverlappingAvailabilityException should return 409
        $response->assertStatus(409)
            ->assertJson([
                'message' => 'The new availability overlaps with an existing one.',
            ]);
    }

    public function test_can_list_availabilities()
    {
        Availability::create([
            'doctor_id' => $this->doctor->id,
            'starts_at' => now()->addDays(3)->setHour(8)->setMinute(0),
            'ends_at' => now()->addDays(3)->setHour(10)->setMinute(0),
            'slot_duration' => 15,
        ]);

        $response = $this->getJson(route('v1.doctors.availabilities.listAvailabilitiesForDoctor', ['doctor' => $this->doctor->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_global_free_slots()
    {
        Availability::create([
            'doctor_id' => $this->doctor->id,
            'starts_at' => now()->addDays(4)->setHour(9)->setMinute(0),
            'ends_at' => now()->addDays(4)->setHour(10)->setMinute(0),
            'slot_duration' => 30,
        ]);
        // This availability yields two slots: 09:00 and 09:30.

        $from = now()->addDays(4)->setHour(0)->setMinute(0)->toDateTimeString();
        $to = now()->addDays(4)->setHour(23)->setMinute(59)->toDateTimeString();

        $response = $this->getJson(route('v1.free-slots.getFreeSlots', [
            'from' => $from,
            'to' => $to,
        ]));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(2, $data);
        $this->assertEquals($this->doctor->id, $data[0]['doctor_id']);
    }
}
