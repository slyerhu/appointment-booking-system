<?php

namespace App\Doctor\DataTransferObjects;

use App\Support\Enums\RequestKeyEnum;

readonly class CreateAvailabilityDataDTO
{
    public function __construct(
        public int $doctorId,
        public string $startsAt,
        public string $endsAt,
        public int $slotDuration = 30,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data[RequestKeyEnum::DOCTOR_ID->value],
            $data[RequestKeyEnum::STARTS_AT->value],
            $data[RequestKeyEnum::ENDS_AT->value],
            $data[RequestKeyEnum::SLOT_DURATION->value] ?? 30
        );
    }
}
