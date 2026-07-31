<?php

namespace App\Appointment\Repositories;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Appointment\Models\Appointment;
use App\Support\Enums\RequestKeyEnum;
use Carbon\CarbonInterface;

class AppointmentWriteRepository
{
    /**
     * @param  array{patient_id: int, doctor_id: int, start_time: CarbonInterface, end_time: CarbonInterface, status: AppointmentStatusEnum}  $data
     */
    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function updateStatus(Appointment $appointment, AppointmentStatusEnum $status, ?string $reason = null): Appointment
    {
        $attributes = [RequestKeyEnum::STATUS->value => $status];
        if ($reason !== null) {
            $attributes[RequestKeyEnum::CANCELLATION_REASON->value] = $reason;
        }

        $appointment->update($attributes);

        return $appointment->fresh(['doctor', 'patient']);
    }
}
