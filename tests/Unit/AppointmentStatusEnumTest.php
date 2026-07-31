<?php

namespace Tests\Unit;

use App\Appointment\Enums\AppointmentStatusEnum;
use PHPUnit\Framework\TestCase;

class AppointmentStatusEnumTest extends TestCase
{
    public function test_pending_can_transition_to_confirmed_and_cancelled()
    {
        $status = AppointmentStatusEnum::PENDING;

        $this->assertTrue($status->canTransitionTo(AppointmentStatusEnum::CONFIRMED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatusEnum::CANCELLED));

        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::PENDING));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::COMPLETED));
    }

    public function test_confirmed_can_transition_to_completed_and_cancelled()
    {
        $status = AppointmentStatusEnum::CONFIRMED;

        $this->assertTrue($status->canTransitionTo(AppointmentStatusEnum::COMPLETED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatusEnum::CANCELLED));

        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::CONFIRMED));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::PENDING));
    }

    public function test_completed_is_terminal_state()
    {
        $status = AppointmentStatusEnum::COMPLETED;

        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::PENDING));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::CONFIRMED));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::CANCELLED));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::COMPLETED));
    }

    public function test_cancelled_is_terminal_state()
    {
        $status = AppointmentStatusEnum::CANCELLED;

        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::PENDING));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::CONFIRMED));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::COMPLETED));
        $this->assertFalse($status->canTransitionTo(AppointmentStatusEnum::CANCELLED));
    }
}
