<?php

namespace App\Doctor\Services;

use App\Doctor\DataTransferObjects\CreateDoctorDataDTO;
use App\Doctor\DataTransferObjects\UpdateDoctorDataDTO;
use App\Doctor\Exceptions\DoctorNotFoundException;
use App\Doctor\Models\Doctor;
use App\Doctor\Repositories\DoctorReadRepository;
use App\Doctor\Repositories\DoctorWriteRepository;
use App\Support\Enums\RequestKeyEnum;

class DoctorService
{
    public function __construct(
        private readonly DoctorReadRepository $doctorReadRepository,
        private readonly DoctorWriteRepository $doctorWriteRepository
    ) {}

    public function createDoctor(CreateDoctorDataDTO $dto): Doctor
    {
        return $this->doctorWriteRepository->create([
            RequestKeyEnum::NAME->value => $dto->name,
            RequestKeyEnum::EMAIL->value => $dto->email,
            RequestKeyEnum::SPECIALTY->value => $dto->specialty,
        ]);
    }

    public function getAllDoctors()
    {
        return $this->doctorReadRepository->all();
    }

    public function getDoctorById(int $id): ?Doctor
    {
        return $this->doctorReadRepository->find($id);
    }

    /**
     * @throws DoctorNotFoundException
     */
    public function updateDoctor(int $id, UpdateDoctorDataDTO $dto): Doctor
    {
        $doctor = $this->doctorReadRepository->find($id);

        if (! $doctor) {
            throw new DoctorNotFoundException($id);
        }

        $data = [];

        if ($dto->name !== null && $dto->name !== $doctor->name) {
            $data[RequestKeyEnum::NAME->value] = $dto->name;
        }

        if ($dto->email !== null && $dto->email !== $doctor->email) {
            $data[RequestKeyEnum::EMAIL->value] = $dto->email;
        }

        if ($dto->specialty !== null && $dto->specialty !== $doctor->specialty) {
            $data[RequestKeyEnum::SPECIALTY->value] = $dto->specialty;
        }

        if (! empty($data)) {
            $doctor = $this->doctorWriteRepository->update($doctor, $data);
        }

        return $doctor;
    }

    /**
     * @throws DoctorNotFoundException
     */
    public function deleteDoctor(int $id): bool
    {
        $doctor = $this->doctorReadRepository->find($id);

        if (! $doctor) {
            throw new DoctorNotFoundException($id);
        }

        return $this->doctorWriteRepository->delete($doctor);
    }
}
