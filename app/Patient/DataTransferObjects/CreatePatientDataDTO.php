<?php

namespace App\Patient\DataTransferObjects;

use App\Support\Enums\RequestKeyEnum;

readonly class CreatePatientDataDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data[RequestKeyEnum::NAME->value],
            $data[RequestKeyEnum::EMAIL->value],
            $data[RequestKeyEnum::PHONE->value]
        );
    }
}
