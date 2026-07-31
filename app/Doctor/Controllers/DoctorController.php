<?php

namespace App\Doctor\Controllers;

use App\Doctor\Exceptions\DoctorNotFoundException;
use App\Doctor\Requests\CreateDoctorRequest;
use App\Doctor\Requests\UpdateDoctorRequest;
use App\Doctor\Resources\DoctorResource;
use App\Doctor\Services\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DoctorController
{
    public function __construct(
        private readonly DoctorService $doctorService
    ) {}

    public function listDoctors(): AnonymousResourceCollection
    {
        return DoctorResource::collection($this->doctorService->getAllDoctors());
    }

    public function createDoctor(CreateDoctorRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->createDoctor($request->toDto());

        return DoctorResource::make($doctor)
            ->response()
            ->setStatusCode(201);
    }

    public function getDoctor(int $id): DoctorResource
    {
        $doctor = $this->doctorService->getDoctorById($id);

        if (! $doctor) {
            throw new DoctorNotFoundException($id);
        }

        return DoctorResource::make($doctor);
    }

    public function updateDoctor(UpdateDoctorRequest $request, int $id): DoctorResource
    {
        $doctor = $this->doctorService->updateDoctor($id, $request->toDto());

        return DoctorResource::make($doctor);
    }

    public function deleteDoctor(int $id): JsonResponse
    {
        $this->doctorService->deleteDoctor($id);

        return response()->json(['message' => 'Doctor deleted successfully'], 200);
    }
}
