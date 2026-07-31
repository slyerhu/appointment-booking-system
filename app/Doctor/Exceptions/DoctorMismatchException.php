<?php

namespace App\Doctor\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorMismatchException extends Exception
{
    public function __construct(int $routeId, int $dtoId, ?Exception $previous = null)
    {
        parent::__construct("Doctor ID mismatch: Route parameter ({$routeId}) does not match the request payload ({$dtoId}).", 400, $previous);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
