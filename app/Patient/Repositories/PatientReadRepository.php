<?php

namespace App\Patient\Repositories;

use App\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class PatientReadRepository
{
    /**
     * @return Collection<int, Patient>
     */
    public function all(): Collection
    {
        return Patient::all();
    }

    public function find(int $id): ?Patient
    {
        return Patient::find($id);
    }
}
