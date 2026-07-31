<?php

namespace Tests\Unit;

use App\Appointment\DataTransferObjects\CreateAppointmentDataDTO;
use App\Appointment\Exceptions\SlotNotAvailableException;
use App\Appointment\Repositories\AppointmentReadRepository;
use App\Appointment\Repositories\AppointmentWriteRepository;
use App\Appointment\Services\AppointmentService;
use App\Doctor\Models\Availability;
use App\Doctor\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AppointmentServiceTest extends TestCase
{
    public function test_booking_fails_if_slot_not_available()
    {
        $readRepo = Mockery::mock(AppointmentReadRepository::class);
        $writeRepo = Mockery::mock(AppointmentWriteRepository::class);
        $availService = Mockery::mock(AvailabilityService::class);

        $service = new AppointmentService($readRepo, $writeRepo, $availService);

        $dto = new CreateAppointmentDataDTO(1, 1, Carbon::now()->toDateTimeString());

        // Mock DB transaction
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Mock Cache lock
        $lock = Mockery::mock();
        $lock->shouldReceive('block')->once()->andReturnUsing(function ($seconds, $callback) {
            return $callback();
        });
        Cache::shouldReceive('lock')->once()->andReturn($lock);

        // AvailabilityService returns null (no availability)
        $availService->shouldReceive('getAvailabilityAt')->once()->andReturn(null);

        $this->expectException(SlotNotAvailableException::class);

        $service->bookAppointment($dto);
    }

    public function test_booking_fails_if_slot_timing_is_invalid()
    {
        $readRepo = Mockery::mock(AppointmentReadRepository::class);
        $writeRepo = Mockery::mock(AppointmentWriteRepository::class);
        $availService = Mockery::mock(AvailabilityService::class);

        $service = new AppointmentService($readRepo, $writeRepo, $availService);

        // Try to book at 09:15
        $startTime = Carbon::now()->setHour(9)->setMinute(15);
        $dto = new CreateAppointmentDataDTO(1, 1, $startTime->toDateTimeString());

        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        $lock = Mockery::mock();
        $lock->shouldReceive('block')->once()->andReturnUsing(function ($seconds, $callback) {
            return $callback();
        });
        Cache::shouldReceive('lock')->once()->andReturn($lock);

        // Availability is 09:00 to 11:00 with 30-minute slots. 09:15 is invalid.
        $availability = new Availability([
            'starts_at' => Carbon::now()->setHour(9)->setMinute(0)->toDateTimeString(),
            'ends_at' => Carbon::now()->setHour(11)->setMinute(0)->toDateTimeString(),
            'slot_duration' => 30,
        ]);

        $availService->shouldReceive('getAvailabilityAt')->once()->andReturn($availability);

        $this->expectException(SlotNotAvailableException::class);

        $service->bookAppointment($dto);
    }
}
