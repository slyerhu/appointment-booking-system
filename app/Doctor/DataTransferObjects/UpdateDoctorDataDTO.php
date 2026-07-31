<?php

namespace App\Doctor\DataTransferObjects;

use App\Support\Enums\RequestKeyEnum;

class UpdateDoctorDataDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $specialty = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data[RequestKeyEnum::NAME->value] ?? null,
            email: $data[RequestKeyEnum::EMAIL->value] ?? null,
            specialty: $data[RequestKeyEnum::SPECIALTY->value] ?? null,
        );
    }
}
