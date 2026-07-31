<?php

namespace App\Support\Enums;

enum RequestKeyEnum: string
{
    case DOCTOR_ID = 'doctor_id';
    case PATIENT_ID = 'patient_id';
    case START_TIME = 'start_time';
    case END_TIME = 'end_time';
    case STARTS_AT = 'starts_at';
    case ENDS_AT = 'ends_at';
    case SLOT_DURATION = 'slot_duration';
    case STATUS = 'status';
    case CANCELLATION_REASON = 'cancellation_reason';
    case FROM = 'from';
    case TO = 'to';
    case NAME = 'name';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case SPECIALTY = 'specialty';
}
