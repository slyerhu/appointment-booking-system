<?php

namespace App\Doctor\Controllers;

use App\Doctor\Exceptions\DoctorMismatchException;
use App\Doctor\Requests\CreateAvailabilityRequest;
use App\Doctor\Requests\GetGlobalFreeSlotsRequest;
use App\Doctor\Resources\AvailabilityResource;
use App\Doctor\Services\AvailabilityService;
use App\Support\Enums\RequestKeyEnum;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AvailabilityController
{
    public function __construct(
        private readonly AvailabilityService $availabilityService
    ) {}

    public function listAvailabilitiesForDoctor(int $doctorId): AnonymousResourceCollection
    {
        return AvailabilityResource::collection(
            $this->availabilityService->getAvailabilitiesForDoctor($doctorId)
        );
    }

    public function createAvailabilityForDoctor(CreateAvailabilityRequest $request, int $doctorId): JsonResponse
    {
        $dto = $request->toDto();

        // Ensure the doctorId in DTO matches the route
        if ($dto->doctorId !== $doctorId) {
            throw new DoctorMismatchException($doctorId, $dto->doctorId);
        }

        $availability = $this->availabilityService->createAvailability($dto);

        return AvailabilityResource::make($availability)
            ->response()
            ->setStatusCode(201);
    }

    public function getFreeSlots(GetGlobalFreeSlotsRequest $request): JsonResponse
    {
        $doctorId = $request->validated(RequestKeyEnum::DOCTOR_ID->value);
        $from = Carbon::parse($request->validated(RequestKeyEnum::FROM->value));
        $to = Carbon::parse($request->validated(RequestKeyEnum::TO->value));
        $page = request('page', 1);

        $slots = $this->availabilityService->calculateFreeSlots(
            $doctorId ? (int) $doctorId : null,
            $from,
            $to,
            (int) $page
        );

        return response()->json($slots);
    }
}
