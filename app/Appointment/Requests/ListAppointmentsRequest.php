<?php

namespace App\Appointment\Requests;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListAppointmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::STATUS->value => ['nullable', Rule::enum(AppointmentStatusEnum::class)],
        ];
    }
}
