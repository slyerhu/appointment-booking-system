<?php

namespace App\Appointment\Controllers;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Appointment\Requests\CreateAppointmentRequest;
use App\Appointment\Requests\ListAppointmentsRequest;
use App\Appointment\Requests\UpdateAppointmentStatusRequest;
use App\Appointment\Resources\AppointmentResource;
use App\Appointment\Services\AppointmentService;
use App\Support\Enums\RequestKeyEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController
{
    public function __construct(
        private readonly AppointmentService $appointmentService,
    ) {}

    public function bookAppointment(CreateAppointmentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $appointment = $this->appointmentService->bookAppointment($dto);

        return AppointmentResource::make($appointment)
            ->response()
            ->setStatusCode(201);
    }

    public function updateAppointmentStatus(UpdateAppointmentStatusRequest $request, int $id): AppointmentResource
    {
        $targetStatus = AppointmentStatusEnum::from($request->validated(RequestKeyEnum::STATUS->value));
        $reason = $request->validated(RequestKeyEnum::CANCELLATION_REASON->value);

        $updatedAppointment = $this->appointmentService->changeStatus($id, $targetStatus, $reason);

        return AppointmentResource::make($updatedAppointment);
    }

    public function listAppointmentsForPatient(int $patientId, ListAppointmentsRequest $request): AnonymousResourceCollection
    {
        $statusParam = $request->validated(RequestKeyEnum::STATUS->value);

        $statusFilter = $statusParam
            ? AppointmentStatusEnum::from($statusParam)
            : null;

        $appointments = $this->appointmentService->getAppointmentsForPatient($patientId, $statusFilter);

        return AppointmentResource::collection($appointments);
    }
}
