<?php

namespace App\Doctor\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OverlappingAvailabilityException extends Exception
{
    public function __construct(string $message = 'The new availability overlaps with an existing one.', int $code = Response::HTTP_CONFLICT)
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
