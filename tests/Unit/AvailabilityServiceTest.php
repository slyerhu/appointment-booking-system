<?php

namespace Tests\Unit;

use App\Appointment\Models\Appointment;
use App\Appointment\Repositories\AppointmentReadRepository;
use App\Doctor\Models\Availability;
use App\Doctor\Repositories\AvailabilityReadRepository;
use App\Doctor\Repositories\AvailabilityWriteRepository;
use App\Doctor\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    public function test_generate_free_slots_calculates_correctly_with_bookings()
    {
        $availRepo = Mockery::mock(AvailabilityReadRepository::class);
        $writeRepo = Mockery::mock(AvailabilityWriteRepository::class);
        $appRepo = Mockery::mock(AppointmentReadRepository::class);

        $service = new AvailabilityService($availRepo, $writeRepo, $appRepo);

        // We have an availability from 09:00 to 11:00 with 30-minute slots. (4 slots total)
        $availability = new Availability([
            'doctor_id' => 1,
            'starts_at' => Carbon::now()->setHour(9)->setMinute(0)->toDateTimeString(),
            'ends_at' => Carbon::now()->setHour(11)->setMinute(0)->toDateTimeString(),
            'slot_duration' => 30,
        ]);

        $availRepo->shouldReceive('getAvailabilities')
            ->with(1)
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$availability]));

        // We have one booking at 09:30
        $booking = new Appointment([
            'doctor_id' => 1,
            'start_time' => Carbon::now()->setHour(9)->setMinute(30)->toDateTimeString(),
        ]);

        $appRepo->shouldReceive('getActiveBookedStartTimes')
            ->andReturn(collect([$booking]));

        // No cache locks
        Cache::shouldReceive('has')->andReturn(false);

        $from = Carbon::now()->setHour(0)->setMinute(0);
        $to = Carbon::now()->setHour(23)->setMinute(59);

        $freeSlots = $service->generateFreeSlots(1, $from, $to);

        // Expected slots: 09:00, 10:00, 10:30 (09:30 is booked)
        $this->assertCount(3, $freeSlots);

        $this->assertEquals(Carbon::now()->setHour(9)->setMinute(0)->timestamp, $freeSlots[0]['start_time']->timestamp);
        $this->assertEquals(Carbon::now()->setHour(10)->setMinute(0)->timestamp, $freeSlots[1]['start_time']->timestamp);
        $this->assertEquals(Carbon::now()->setHour(10)->setMinute(30)->timestamp, $freeSlots[2]['start_time']->timestamp);
    }
}
