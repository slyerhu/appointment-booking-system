<?php

namespace App\Appointment\Enums;

enum AppointmentStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $targetStatus): bool
    {
        if ($this === $targetStatus) {
            return false;
        }

        return match ($this) {
            self::PENDING => in_array($targetStatus, [self::CONFIRMED, self::CANCELLED]),
            self::CONFIRMED => in_array($targetStatus, [self::COMPLETED, self::CANCELLED]),
            self::COMPLETED, self::CANCELLED => false, // Terminal states
        };
    }
}
