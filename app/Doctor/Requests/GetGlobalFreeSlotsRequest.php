<?php

namespace App\Doctor\Requests;

use App\Support\Enums\RequestKeyEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetGlobalFreeSlotsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            RequestKeyEnum::DOCTOR_ID->value => ['nullable', 'integer', 'exists:doctors,id'],
            RequestKeyEnum::FROM->value => ['required', 'date'],
            RequestKeyEnum::TO->value => ['required', 'date', 'after_or_equal:'.RequestKeyEnum::FROM->value],
        ];
    }
}
