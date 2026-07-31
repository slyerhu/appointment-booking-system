<?php

namespace App\Doctor\Requests;

use App\Doctor\DataTransferObjects\CreateDoctorDataDTO;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;

class CreateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::NAME->value => ['required', 'string', 'max:255'],
            RequestKeyEnum::EMAIL->value => ['required', 'email', 'unique:doctors,email'],
            RequestKeyEnum::SPECIALTY->value => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): CreateDoctorDataDTO
    {
        return CreateDoctorDataDTO::fromArray($this->validated());
    }
}
