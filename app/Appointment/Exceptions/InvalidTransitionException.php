<?php

namespace App\Appointment\Exceptions;

use App\Appointment\Enums\AppointmentStatusEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvalidTransitionException extends Exception
{
    public function __construct(AppointmentStatusEnum $from, AppointmentStatusEnum $to)
    {
        parent::__construct("Invalid status transition from {$from->value} to {$to->value}.", 422);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
