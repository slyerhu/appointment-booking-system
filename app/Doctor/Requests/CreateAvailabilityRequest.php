<?php

namespace App\Doctor\Requests;

use App\Doctor\DataTransferObjects\CreateAvailabilityDataDTO;
use App\Support\Enums\RequestKeyEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('doctor')) {
            $this->merge([
                RequestKeyEnum::DOCTOR_ID->value => $this->route('doctor'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::DOCTOR_ID->value => ['required', 'integer', Rule::exists('doctors', 'id')],
            RequestKeyEnum::STARTS_AT->value => ['required', 'date', 'after:now'],
            RequestKeyEnum::ENDS_AT->value => ['required', 'date', 'after:'.RequestKeyEnum::STARTS_AT->value],
            RequestKeyEnum::SLOT_DURATION->value => ['nullable', 'integer', 'min:30'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->messages()->isNotEmpty()) {
                return;
            }

            $startsAt = Carbon::parse($this->input(RequestKeyEnum::STARTS_AT->value));
            $endsAt = Carbon::parse($this->input(RequestKeyEnum::ENDS_AT->value));
            $slotDuration = (int) $this->input(RequestKeyEnum::SLOT_DURATION->value, 30);

            $durationInMinutes = $startsAt->diffInMinutes($endsAt);

            if ($durationInMinutes < $slotDuration) {
                $validator->errors()->add(
                    RequestKeyEnum::ENDS_AT->value,
                    "The availability duration ({$durationInMinutes} minutes) must be at least the slot duration ({$slotDuration} minutes)."
                );
            } elseif ($durationInMinutes % $slotDuration !== 0) {
                $validator->errors()->add(
                    RequestKeyEnum::ENDS_AT->value,
                    "The availability duration ({$durationInMinutes} minutes) must be an exact multiple of the slot duration ({$slotDuration} minutes)."
                );
            }
        });
    }

    public function toDto(): CreateAvailabilityDataDTO
    {
        return CreateAvailabilityDataDTO::fromArray($this->validated());
    }
}
