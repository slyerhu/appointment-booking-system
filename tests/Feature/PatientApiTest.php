<?php

namespace Tests\Feature;

use App\Patient\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_patients()
    {
        Patient::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);
        Patient::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
        ]);

        $response = $this->getJson(route('v1.patients.listPatients'));

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_patient()
    {
        $response = $this->postJson(route('v1.patients.createPatient'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'phone' => '111222333',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'phone' => '111222333',
            ]);

        $this->assertDatabaseHas('patients', ['email' => 'alice@example.com']);
    }

    public function test_can_get_patient()
    {
        $patient = Patient::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'phone' => '444555666',
        ]);

        $response = $this->getJson(route('v1.patients.getPatient', ['patient' => $patient->id]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Bob',
            ]);
    }

    public function test_can_update_patient()
    {
        $patient = Patient::create([
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
            'phone' => '777888999',
        ]);

        $response = $this->putJson(route('v1.patients.updatePatient', ['patient' => $patient->id]), [
            'name' => 'Charles',
            'phone' => '000000000',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Charles',
                'phone' => '000000000',
            ]);

        $this->assertEquals('charlie@example.com', $response->json('data.email'));
    }

    public function test_can_delete_patient()
    {
        $patient = Patient::create([
            'name' => 'Dave',
            'email' => 'dave@example.com',
            'phone' => '123123123',
        ]);

        $response = $this->deleteJson(route('v1.patients.deletePatient', ['patient' => $patient->id]));

        $response->assertStatus(200);

        $this->assertSoftDeleted($patient);
    }

    public function test_returns_404_for_missing_patient()
    {
        $response = $this->getJson(route('v1.patients.getPatient', ['patient' => 999]));
        $response->assertStatus(404);

        $response = $this->putJson(route('v1.patients.updatePatient', ['patient' => 999]), ['name' => 'Test']);
        $response->assertStatus(404);

        $response = $this->deleteJson(route('v1.patients.deletePatient', ['patient' => 999]));
        $response->assertStatus(404);
    }
}
