<?php

namespace App\Appointment\Exceptions;

use Carbon\CarbonInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotNotAvailableException extends Exception
{
    public function __construct(CarbonInterface $time, ?Exception $previous = null)
    {
        parent::__construct("The time slot at {$time->toDateTimeString()} is not available.", 422, $previous);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
