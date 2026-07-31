<?php

namespace App\Doctor\DataTransferObjects;

use App\Support\Enums\RequestKeyEnum;

readonly class CreateDoctorDataDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $specialty,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data[RequestKeyEnum::NAME->value],
            $data[RequestKeyEnum::EMAIL->value],
            $data[RequestKeyEnum::SPECIALTY->value]
        );
    }
}
