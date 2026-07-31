<?php

namespace App\Doctor\Requests;

use App\Support\Enums\RequestKeyEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetFreeSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            RequestKeyEnum::FROM->value => ['required', 'date'],
            RequestKeyEnum::TO->value => ['required', 'date', 'after_or_equal:'.RequestKeyEnum::FROM->value],
        ];
    }
}
