<?php

namespace Tests\Feature;

use App\Doctor\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_doctors()
    {
        Doctor::create([
            'name' => 'Dr. Smith',
            'email' => 'smith@example.com',
            'specialty' => 'Cardiology',
        ]);
        Doctor::create([
            'name' => 'Dr. Jones',
            'email' => 'jones@example.com',
            'specialty' => 'Neurology',
        ]);

        $response = $this->getJson(route('v1.doctors.listDoctors'));

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_doctor()
    {
        $response = $this->postJson(route('v1.doctors.createDoctor'), [
            'name' => 'Dr. Strange',
            'email' => 'strange@example.com',
            'specialty' => 'Surgery',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Dr. Strange',
                'email' => 'strange@example.com',
                'specialty' => 'Surgery',
            ]);

        $this->assertDatabaseHas('doctors', ['email' => 'strange@example.com']);
    }

    public function test_can_get_doctor()
    {
        $doctor = Doctor::create([
            'name' => 'Dr. Who',
            'email' => 'who@example.com',
            'specialty' => 'Time Travel',
        ]);

        $response = $this->getJson(route('v1.doctors.getDoctor', ['doctor' => $doctor->id]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Dr. Who',
            ]);
    }

    public function test_can_update_doctor()
    {
        $doctor = Doctor::create([
            'name' => 'Dr. House',
            'email' => 'house@example.com',
            'specialty' => 'Diagnostics',
        ]);

        $response = $this->putJson(route('v1.doctors.updateDoctor', ['doctor' => $doctor->id]), [
            'name' => 'Dr. Gregory House',
            'specialty' => 'Infectious Diseases',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Dr. Gregory House',
                'specialty' => 'Infectious Diseases',
            ]);

        $this->assertEquals('house@example.com', $response->json('data.email')); // Should remain unchanged
    }

    public function test_can_delete_doctor()
    {
        $doctor = Doctor::create([
            'name' => 'Dr. Doom',
            'email' => 'doom@example.com',
            'specialty' => 'Villainy',
        ]);

        $response = $this->deleteJson(route('v1.doctors.deleteDoctor', ['doctor' => $doctor->id]));

        $response->assertStatus(200);

        $this->assertSoftDeleted($doctor);
    }

    public function test_returns_404_for_missing_doctor()
    {
        $response = $this->getJson(route('v1.doctors.getDoctor', ['doctor' => 999]));
        $response->assertStatus(404);

        $response = $this->putJson(route('v1.doctors.updateDoctor', ['doctor' => 999]), ['name' => 'Test']);
        $response->assertStatus(404);

        $response = $this->deleteJson(route('v1.doctors.deleteDoctor', ['doctor' => 999]));
        $response->assertStatus(404);
    }
}
