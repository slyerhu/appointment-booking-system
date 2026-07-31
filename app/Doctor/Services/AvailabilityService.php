<?php

namespace App\Doctor\Services;

use App\Appointment\Repositories\AppointmentReadRepository;
use App\Doctor\DataTransferObjects\CreateAvailabilityDataDTO;
use App\Doctor\Exceptions\OverlappingAvailabilityException;
use App\Doctor\Models\Availability;
use App\Doctor\Repositories\AvailabilityReadRepository;
use App\Doctor\Repositories\AvailabilityWriteRepository;
use App\Support\Enums\CacheKeyEnum;
use App\Support\Enums\RequestKeyEnum;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AvailabilityService
{
    public function __construct(
        private readonly AvailabilityReadRepository $availabilityReadRepository,
        private readonly AvailabilityWriteRepository $availabilityWriteRepository,
        private readonly AppointmentReadRepository $appointmentReadRepository
    ) {}

    /**
     * @throws OverlappingAvailabilityException
     */
    public function createAvailability(CreateAvailabilityDataDTO $dto): Availability
    {
        $startsAt = Carbon::parse($dto->startsAt);
        $endsAt = Carbon::parse($dto->endsAt);

        if ($this->availabilityReadRepository->hasOverlapping($dto->doctorId, $startsAt, $endsAt)) {
            throw new OverlappingAvailabilityException;
        }

        return $this->availabilityWriteRepository->create([
            RequestKeyEnum::DOCTOR_ID->value => $dto->doctorId,
            RequestKeyEnum::STARTS_AT->value => $dto->startsAt,
            RequestKeyEnum::ENDS_AT->value => $dto->endsAt,
            RequestKeyEnum::SLOT_DURATION->value => $dto->slotDuration,
        ]);
    }

    public function getAvailabilityAt(int $doctorId, CarbonInterface $time): ?Availability
    {
        return $this->availabilityReadRepository->getForDoctorAt($doctorId, $time);
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilitiesForDoctor(int $doctorId): Collection
    {
        return $this->availabilityReadRepository->getForDoctor($doctorId);
    }

    /**
     * @return Collection<int, array{doctor_id: int, start_time: CarbonInterface}>
     */
    public function generateFreeSlots(?int $doctorId, CarbonInterface $from, CarbonInterface $to): Collection
    {
        $availabilities = $this->availabilityReadRepository->getAvailabilities($doctorId);
        $bookedAppointments = $this->appointmentReadRepository->getActiveBookedStartTimes($doctorId, $from, $to);

        $freeSlots = collect();

        foreach ($availabilities as $availability) {
            $currentSlotStart = Carbon::parse($availability->starts_at);
            $availabilityEnd = Carbon::parse($availability->ends_at);
            $duration = $availability->slot_duration;

            while ($currentSlotStart->copy()->addMinutes($duration)->lte($availabilityEnd)) {
                if ($currentSlotStart->gte($from) && $currentSlotStart->lte($to)) {
                    $lockKey = CacheKeyEnum::DOCTOR_APPOINTMENT_LOCK->format($availability->doctor_id, $currentSlotStart->timestamp);

                    $isBooked = $bookedAppointments->contains(function ($booking) use ($availability, $currentSlotStart) {
                        return $booking->doctor_id === $availability->doctor_id
                            && Carbon::parse($booking->start_time)->equalTo($currentSlotStart);
                    });

                    if (! $isBooked && ! Cache::has($lockKey)) {
                        $freeSlots->push([
                            'doctor_id' => $availability->doctor_id,
                            'start_time' => $currentSlotStart->copy(),
                        ]);
                    }
                }
                $currentSlotStart->addMinutes($duration);
            }
        }

        return $freeSlots->sortBy(fn ($slot) => $slot['start_time']->timestamp)->values();
    }

    /**
     * @return LengthAwarePaginator<array{doctor_id: int, start_time: string}>
     */
    public function calculateFreeSlots(?int $doctorId, CarbonInterface $from, CarbonInterface $to, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        $freeSlots = $this->generateFreeSlots($doctorId, $from, $to);

        // Format to string for API response
        $freeSlots = $freeSlots->map(fn ($slot) => [
            'doctor_id' => $slot['doctor_id'],
            'start_time' => $slot['start_time']->toDateTimeString(),
        ]);

        $total = $freeSlots->count();
        $items = $freeSlots->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
