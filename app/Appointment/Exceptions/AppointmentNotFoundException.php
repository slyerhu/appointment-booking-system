<?php

namespace App\Appointment\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentNotFoundException extends Exception
{
    public function __construct(int $appointmentId)
    {
        parent::__construct("Appointment with ID {$appointmentId} not found.", 404);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
