<?php

namespace App\Doctor\Repositories;

use App\Doctor\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;

class DoctorReadRepository
{
    /**
     * @return Collection<int, Doctor>
     */
    public function all(): Collection
    {
        return Doctor::all();
    }

    public function find(int $id): ?Doctor
    {
        return Doctor::find($id);
    }
}
