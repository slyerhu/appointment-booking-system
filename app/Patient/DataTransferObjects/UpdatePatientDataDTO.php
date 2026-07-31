<?php

namespace App\Patient\DataTransferObjects;

use App\Support\Enums\RequestKeyEnum;

class UpdatePatientDataDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data[RequestKeyEnum::NAME->value] ?? null,
            email: $data[RequestKeyEnum::EMAIL->value] ?? null,
            phone: $data[RequestKeyEnum::PHONE->value] ?? null,
        );
    }
}
