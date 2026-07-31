<?php

namespace App\Support\Enums;

enum CacheKeyEnum: string
{
    case DOCTOR_APPOINTMENT_LOCK = 'book:doctor:%d:%d';

    public function format(...$args): string
    {
        return sprintf($this->value, ...$args);
    }
}
