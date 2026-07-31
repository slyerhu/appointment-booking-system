<?php

namespace App\Appointment\Services;

use App\Appointment\DataTransferObjects\CreateAppointmentDataDTO;
use App\Appointment\Enums\AppointmentStatusEnum;
use App\Appointment\Exceptions\AppointmentNotFoundException;
use App\Appointment\Exceptions\CancellationTooLateException;
use App\Appointment\Exceptions\InvalidTransitionException;
use App\Appointment\Exceptions\PatientDoubleBookingException;
use App\Appointment\Exceptions\SlotNotAvailableException;
use App\Appointment\Models\Appointment;
use App\Appointment\Repositories\AppointmentReadRepository;
use App\Appointment\Repositories\AppointmentWriteRepository;
use App\Doctor\Models\Availability;
use App\Doctor\Services\AvailabilityService;
use App\Support\Enums\CacheKeyEnum;
use App\Support\Enums\RequestKeyEnum;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentReadRepository $appointmentReadRepository,
        private readonly AppointmentWriteRepository $appointmentWriteRepository,
        private readonly AvailabilityService $availabilityService,
    ) {}

    /**
     * @throws SlotNotAvailableException
     * @throws PatientDoubleBookingException
     */
    public function bookAppointment(CreateAppointmentDataDTO $dto): Appointment
    {
        $startTime = Carbon::parse($dto->startTime);
        $lockKey = CacheKeyEnum::DOCTOR_APPOINTMENT_LOCK->format($dto->doctorId, $startTime->timestamp);

        $lockDuration = config('appointment.lock_duration_seconds', 10);
        $lockBlock = config('appointment.lock_block_seconds', 5);

        return DB::transaction(function () use ($dto, $startTime, $lockKey, $lockDuration, $lockBlock) {
            return Cache::lock($lockKey, $lockDuration)->block($lockBlock, function () use ($dto, $startTime) {
                $availability = $this->availabilityService->getAvailabilityAt($dto->doctorId, $startTime);

                if (! $availability) {
                    throw new SlotNotAvailableException($startTime);
                }

                $endTime = $startTime->copy()->addMinutes($availability->slot_duration);

                $this->ensureValidSlotTiming($availability, $startTime, $endTime);
                $this->ensureDoctorIsFree($dto->doctorId, $startTime, $endTime);
                $this->ensurePatientIsFree($dto->patientId, $startTime, $endTime);

                return $this->createAppointment($dto, $startTime, $endTime);
            });
        });
    }

    /**
     * @throws SlotNotAvailableException
     */
    private function ensureValidSlotTiming(Availability $availability, CarbonInterface $startTime, CarbonInterface $endTime): void
    {
        $availabilityStart = Carbon::parse($availability->starts_at);
        $availabilityEnd = Carbon::parse($availability->ends_at);

        if ($startTime->lt($availabilityStart) || $endTime->gt($availabilityEnd)) {
            throw new SlotNotAvailableException($startTime);
        }

        $diffInMinutes = $availabilityStart->diffInMinutes($startTime);

        if (($diffInMinutes % $availability->slot_duration) !== 0) {
            throw new SlotNotAvailableException($startTime);
        }
    }

    /**
     * @throws SlotNotAvailableException
     */
    private function ensureDoctorIsFree(int $doctorId, CarbonInterface $startTime, CarbonInterface $endTime): void
    {
        if ($this->appointmentReadRepository->hasDoctorOverlappingBooking($doctorId, $startTime, $endTime)) {
            throw new SlotNotAvailableException($startTime);
        }
    }

    /**
     * @throws PatientDoubleBookingException
     */
    private function ensurePatientIsFree(int $patientId, CarbonInterface $startTime, CarbonInterface $endTime): void
    {
        if ($this->appointmentReadRepository->hasPatientOverlappingBooking($patientId, $startTime, $endTime)) {
            throw new PatientDoubleBookingException($patientId, $startTime);
        }
    }

    /**
     * @throws SlotNotAvailableException
     */
    private function createAppointment(CreateAppointmentDataDTO $dto, CarbonInterface $startTime, CarbonInterface $endTime): Appointment
    {
        try {
            return $this->appointmentWriteRepository->create([
                RequestKeyEnum::PATIENT_ID->value => $dto->patientId,
                RequestKeyEnum::DOCTOR_ID->value => $dto->doctorId,
                RequestKeyEnum::START_TIME->value => $startTime,
                RequestKeyEnum::END_TIME->value => $endTime,
                RequestKeyEnum::STATUS->value => AppointmentStatusEnum::PENDING,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new SlotNotAvailableException($startTime, previous: $e);
        }
    }

    /**
     * @return LengthAwarePaginator<Appointment>
     */
    public function getAppointmentsForPatient(int $patientId, ?AppointmentStatusEnum $statusFilter = null): LengthAwarePaginator
    {
        return $this->appointmentReadRepository->paginateForPatient($patientId, $statusFilter);
    }

    /**
     * @throws AppointmentNotFoundException
     * @throws InvalidTransitionException
     * @throws CancellationTooLateException
     */
    public function changeStatus(int $appointmentId, AppointmentStatusEnum $targetStatus, ?string $reason = null): Appointment
    {
        $appointment = $this->appointmentReadRepository->findById($appointmentId);

        if (! $appointment) {
            throw new AppointmentNotFoundException($appointmentId);
        }

        if (! $appointment->status->canTransitionTo($targetStatus)) {
            throw new InvalidTransitionException($appointment->status, $targetStatus);
        }

        if ($targetStatus === AppointmentStatusEnum::CANCELLED && $appointment->status === AppointmentStatusEnum::CONFIRMED) {
            if (now()->diffInHours($appointment->start_time, false) < config('appointment.cancellation_window_hours', 24)) {
                throw new CancellationTooLateException($appointment);
            }
        }

        return $this->appointmentWriteRepository->updateStatus($appointment, $targetStatus, $reason);
    }
}
