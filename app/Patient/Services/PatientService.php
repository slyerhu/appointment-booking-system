<?php

namespace App\Patient\Services;

use App\Patient\DataTransferObjects\CreatePatientDataDTO;
use App\Patient\DataTransferObjects\UpdatePatientDataDTO;
use App\Patient\Exceptions\PatientNotFoundException;
use App\Patient\Models\Patient;
use App\Patient\Repositories\PatientReadRepository;
use App\Patient\Repositories\PatientWriteRepository;
use App\Support\Enums\RequestKeyEnum;

class PatientService
{
    public function __construct(
        private readonly PatientReadRepository $patientReadRepository,
        private readonly PatientWriteRepository $patientWriteRepository
    ) {}

    public function createPatient(CreatePatientDataDTO $dto): Patient
    {
        return $this->patientWriteRepository->create([
            RequestKeyEnum::NAME->value => $dto->name,
            RequestKeyEnum::EMAIL->value => $dto->email,
            RequestKeyEnum::PHONE->value => $dto->phone,
        ]);
    }

    public function getAllPatients()
    {
        return $this->patientReadRepository->all();
    }

    public function getPatientById(int $id): ?Patient
    {
        return $this->patientReadRepository->find($id);
    }

    /**
     * @throws PatientNotFoundException
     */
    public function updatePatient(int $id, UpdatePatientDataDTO $dto): Patient
    {
        $patient = $this->patientReadRepository->find($id);

        if (! $patient) {
            throw new PatientNotFoundException($id);
        }

        $data = [];

        if ($dto->name !== null && $dto->name !== $patient->name) {
            $data[RequestKeyEnum::NAME->value] = $dto->name;
        }

        if ($dto->email !== null && $dto->email !== $patient->email) {
            $data[RequestKeyEnum::EMAIL->value] = $dto->email;
        }

        if ($dto->phone !== null && $dto->phone !== $patient->phone) {
            $data[RequestKeyEnum::PHONE->value] = $dto->phone;
        }

        if (! empty($data)) {
            $patient = $this->patientWriteRepository->update($patient, $data);
        }

        return $patient;
    }

    /**
     * @throws PatientNotFoundException
     */
    public function deletePatient(int $id): bool
    {
        $patient = $this->patientReadRepository->find($id);

        if (! $patient) {
            throw new PatientNotFoundException($id);
        }

        return $this->patientWriteRepository->delete($patient);
    }
}
