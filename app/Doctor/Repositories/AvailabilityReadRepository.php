<?php

namespace App\Doctor\Repositories;

use App\Doctor\Models\Availability;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityReadRepository
{
    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(?int $doctorId = null): Collection
    {
        return Availability::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->get();
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getForDoctor(int $doctorId): Collection
    {
        return $this->getAvailabilities($doctorId);
    }

    public function hasOverlapping(int $doctorId, CarbonInterface $startsAt, CarbonInterface $endsAt): bool
    {
        return Availability::where('doctor_id', $doctorId)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }

    public function getForDoctorAt(int $doctorId, CarbonInterface $time): ?Availability
    {
        return Availability::where('doctor_id', $doctorId)
            ->where('starts_at', '<=', $time)
            ->where('ends_at', '>', $time)
            ->first();
    }
}
