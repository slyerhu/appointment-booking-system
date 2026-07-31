<?php

namespace App\Appointment\Models;

use App\Appointment\Enums\AppointmentStatusEnum;
use App\Doctor\Models\Doctor;
use App\Patient\Models\Patient;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return AppointmentFactory::new();
    }

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'start_time',
        'end_time',
        'status',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => AppointmentStatusEnum::class,
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Scope a query to only include active appointments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [AppointmentStatusEnum::PENDING, AppointmentStatusEnum::CONFIRMED]);
    }
}
