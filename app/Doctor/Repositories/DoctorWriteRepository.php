<?php

namespace App\Doctor\Repositories;

use App\Doctor\Models\Doctor;

class DoctorWriteRepository
{
    public function create(array $data): Doctor
    {
        return Doctor::create($data);
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        $doctor->update($data);

        return $doctor;
    }

    public function delete(Doctor $doctor): bool
    {
        return $doctor->delete() ?? false;
    }
}
