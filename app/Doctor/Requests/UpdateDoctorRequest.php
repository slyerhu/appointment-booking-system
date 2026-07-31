<?php

namespace App\Doctor\Requests;

use App\Doctor\DataTransferObjects\UpdateDoctorDataDTO;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::NAME->value => ['sometimes', 'required', 'string', 'max:255'],
            RequestKeyEnum::EMAIL->value => ['sometimes', 'required', 'email', 'unique:doctors,email,'.$this->route('doctor')],
            RequestKeyEnum::SPECIALTY->value => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdateDoctorDataDTO
    {
        return UpdateDoctorDataDTO::fromArray($this->validated());
    }
}
