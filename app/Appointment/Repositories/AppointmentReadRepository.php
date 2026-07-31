<?php

namespace App\Appointment\Repositories;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Appointment\Models\Appointment;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AppointmentReadRepository
{
    public function findById(int $id): ?Appointment
    {
        return Appointment::with(['doctor', 'patient'])->find($id);
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getActiveBookedStartTimes(?int $doctorId, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return Appointment::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->active()
            ->whereBetween('start_time', [$from, $to])
            ->get(['start_time', 'doctor_id']);
    }

    /**
     * @return Collection<int, string>
     */
    public function getActiveBookedStartTimesForDoctor(int $doctorId, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return $this->getActiveBookedStartTimes($doctorId, $from, $to)->pluck('start_time');
    }

    public function hasPatientOverlappingBooking(int $patientId, CarbonInterface $startTime, CarbonInterface $endTime): bool
    {
        return Appointment::query()
            ->where('patient_id', $patientId)
            ->active()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    public function hasDoctorOverlappingBooking(int $doctorId, CarbonInterface $startTime, CarbonInterface $endTime): bool
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->active()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    /**
     * @return LengthAwarePaginator<Appointment>
     */
    public function paginateForPatient(int $patientId, ?AppointmentStatusEnum $statusFilter = null, int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::query()
            ->with(['doctor:id,name,email,specialty'])
            ->where('patient_id', $patientId)
            ->when($statusFilter !== null, fn ($query) => $query->where('status', $statusFilter))
            ->latest('start_time')
            ->paginate($perPage);
    }
}
