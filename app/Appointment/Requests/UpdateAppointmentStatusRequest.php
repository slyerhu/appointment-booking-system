<?php

namespace App\Appointment\Requests;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::STATUS->value => ['required', Rule::enum(AppointmentStatusEnum::class)],
            RequestKeyEnum::CANCELLATION_REASON->value => [
                'nullable',
                'string',
                'max:1000',
                Rule::requiredIf(fn () => $this->input(RequestKeyEnum::STATUS->value) === AppointmentStatusEnum::CANCELLED->value),
            ],
        ];
    }
}
