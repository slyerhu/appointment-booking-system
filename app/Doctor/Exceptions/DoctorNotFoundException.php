<?php

namespace App\Doctor\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorNotFoundException extends Exception
{
    public function __construct(int $id, ?Exception $previous = null)
    {
        parent::__construct("Doctor with ID {$id} not found.", 404, $previous);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
