<?php

namespace App\Appointment\Exceptions;

use App\Appointment\Models\Appointment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CancellationTooLateException extends Exception
{
    public function __construct(Appointment $appointment)
    {
        parent::__construct('Confirmed appointment can only be cancelled at least 24 hours in advance.', 422);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
