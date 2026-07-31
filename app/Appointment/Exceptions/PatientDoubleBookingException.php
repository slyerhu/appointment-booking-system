<?php

namespace App\Appointment\Exceptions;

use Carbon\CarbonInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientDoubleBookingException extends Exception
{
    public function __construct(int $patientId, CarbonInterface $time)
    {
        parent::__construct("Patient {$patientId} already has an active booking at {$time->toDateTimeString()}.", 422);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
