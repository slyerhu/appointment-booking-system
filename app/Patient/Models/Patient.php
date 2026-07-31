<?php

namespace App\Patient\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return PatientFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
}
