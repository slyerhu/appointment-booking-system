<?php

namespace App\Patient\Requests;

use App\Patient\DataTransferObjects\UpdatePatientDataDTO;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::NAME->value => ['sometimes', 'required', 'string', 'max:255'],
            RequestKeyEnum::EMAIL->value => ['sometimes', 'required', 'email', 'unique:patients,email,'.$this->route('patient')],
            RequestKeyEnum::PHONE->value => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdatePatientDataDTO
    {
        return UpdatePatientDataDTO::fromArray($this->validated());
    }
}
