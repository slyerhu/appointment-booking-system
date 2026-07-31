<?php

namespace App\Doctor\Repositories;

use App\Doctor\Models\Availability;

class AvailabilityWriteRepository
{
    public function create(array $data): Availability
    {
        return Availability::create($data);
    }
}
