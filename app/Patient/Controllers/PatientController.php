<?php

namespace App\Patient\Controllers;

use App\Patient\Exceptions\PatientNotFoundException;
use App\Patient\Requests\CreatePatientRequest;
use App\Patient\Requests\UpdatePatientRequest;
use App\Patient\Resources\PatientResource;
use App\Patient\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PatientController
{
    public function __construct(
        private readonly PatientService $patientService
    ) {}

    public function listPatients(): AnonymousResourceCollection
    {
        return PatientResource::collection($this->patientService->getAllPatients());
    }

    public function createPatient(CreatePatientRequest $request): JsonResponse
    {
        $patient = $this->patientService->createPatient($request->toDto());

        return PatientResource::make($patient)
            ->response()
            ->setStatusCode(201);
    }

    public function getPatient(int $id): PatientResource
    {
        $patient = $this->patientService->getPatientById($id);

        if (! $patient) {
            throw new PatientNotFoundException($id);
        }

        return PatientResource::make($patient);
    }

    public function updatePatient(UpdatePatientRequest $request, int $id): PatientResource
    {
        $patient = $this->patientService->updatePatient($id, $request->toDto());

        return PatientResource::make($patient);
    }

    public function deletePatient(int $id): JsonResponse
    {
        $this->patientService->deletePatient($id);

        return response()->json(['message' => 'Patient deleted successfully'], 200);
    }
}
