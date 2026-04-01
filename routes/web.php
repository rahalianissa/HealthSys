<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaitingRoomController;
use App\Http\Controllers\PrescriptionController;
Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::resource('patients', PatientController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('appointments', AppointmentController::class);
    
    // Routes pour le calendrier
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
    Route::get('/api/events', [AppointmentController::class, 'getEvents'])->name('api.events');
    

    // Ajouter dans le groupe auth
    Route::get('/waiting-room', [WaitingRoomController::class, 'index'])->name('waiting-room');
    Route::post('/waiting-room/add', [WaitingRoomController::class, 'add'])->name('waiting-room.add');
    Route::post('/waiting-room/{waitingRoom}/start', [WaitingRoomController::class, 'startConsultation'])->name('waiting-room.start');
    Route::post('/waiting-room/{waitingRoom}/complete', [WaitingRoomController::class, 'complete'])->name('waiting-room.complete');
    Route::delete('/waiting-room/{waitingRoom}/remove', [WaitingRoomController::class, 'remove'])->name('waiting-room.remove');
    

    // Dans le groupe auth
    Route::resource('prescriptions', PrescriptionController::class);
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');
        
    
    });