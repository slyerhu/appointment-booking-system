<?php

namespace App\Patient\Requests;

use App\Patient\DataTransferObjects\CreatePatientDataDTO;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;

class CreatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::NAME->value => ['required', 'string', 'max:255'],
            RequestKeyEnum::EMAIL->value => ['required', 'email', 'unique:patients,email'],
            RequestKeyEnum::PHONE->value => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): CreatePatientDataDTO
    {
        return CreatePatientDataDTO::fromArray($this->validated());
    }
}
