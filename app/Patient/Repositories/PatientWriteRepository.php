<?php

namespace App\Patient\Repositories;

use App\Patient\Models\Patient;

class PatientWriteRepository
{
    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient;
    }

    public function delete(Patient $patient): bool
    {
        return $patient->delete() ?? false;
    }
}
