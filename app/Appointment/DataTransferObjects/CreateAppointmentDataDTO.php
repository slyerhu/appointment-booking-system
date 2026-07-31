<?php

namespace App\Appointment\DataTransferObjects;

use App\Support\Enums\RequestKeyEnum;

readonly class CreateAppointmentDataDTO
{
    public function __construct(
        public int $patientId,
        public int $doctorId,
        public string $startTime,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data[RequestKeyEnum::PATIENT_ID->value],
            $data[RequestKeyEnum::DOCTOR_ID->value],
            $data[RequestKeyEnum::START_TIME->value]
        );
    }
}
