<?php

namespace App\Appointment\Requests;

use App\Appointment\DataTransferObjects\CreateAppointmentDataDTO;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::PATIENT_ID->value => ['required', Rule::exists('patients', 'id')],
            RequestKeyEnum::DOCTOR_ID->value => ['required', Rule::exists('doctors', 'id')],
            RequestKeyEnum::START_TIME->value => ['required', 'date', 'after:now'],
        ];
    }

    public function toDto(): CreateAppointmentDataDTO
    {
        return CreateAppointmentDataDTO::fromArray($this->validated());
    }
}
