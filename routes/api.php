<?php

use App\Appointment\Controllers\AppointmentController;
use App\Doctor\Controllers\AvailabilityController;
use App\Doctor\Controllers\DoctorController;
use App\Patient\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    // Doctors CRUD
    Route::get('doctors', [DoctorController::class, 'listDoctors'])->name('doctors.listDoctors');
    Route::post('doctors', [DoctorController::class, 'createDoctor'])->name('doctors.createDoctor');
    Route::get('doctors/{doctor}', [DoctorController::class, 'getDoctor'])->name('doctors.getDoctor');
    Route::put('doctors/{doctor}', [DoctorController::class, 'updateDoctor'])->name('doctors.updateDoctor');
    Route::delete('doctors/{doctor}', [DoctorController::class, 'deleteDoctor'])->name('doctors.deleteDoctor');

    // Patients CRUD
    Route::get('patients', [PatientController::class, 'listPatients'])->name('patients.listPatients');
    Route::post('patients', [PatientController::class, 'createPatient'])->name('patients.createPatient');
    Route::get('patients/{patient}', [PatientController::class, 'getPatient'])->name('patients.getPatient');
    Route::put('patients/{patient}', [PatientController::class, 'updatePatient'])->name('patients.updatePatient');
    Route::delete('patients/{patient}', [PatientController::class, 'deletePatient'])->name('patients.deletePatient');

    // Doctor Availabilities
    Route::get('doctors/{doctor}/availabilities', [AvailabilityController::class, 'listAvailabilitiesForDoctor'])->name('doctors.availabilities.listAvailabilitiesForDoctor');
    Route::post('doctors/{doctor}/availabilities', [AvailabilityController::class, 'createAvailabilityForDoctor'])->name('doctors.availabilities.createAvailabilityForDoctor');

    // Global Free Slots
    Route::get('free-slots', [AvailabilityController::class, 'getFreeSlots'])->name('free-slots.getFreeSlots');

    // Appointments
    Route::post('appointments', [AppointmentController::class, 'bookAppointment'])->name('appointments.bookAppointment');
    Route::patch('appointments/{appointment}', [AppointmentController::class, 'updateAppointmentStatus'])->name('appointments.updateAppointmentStatus');
    Route::get('patients/{patient}/appointments', [AppointmentController::class, 'listAppointmentsForPatient'])->name('patients.listAppointmentsForPatient');
});
