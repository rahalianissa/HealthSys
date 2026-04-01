<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\WaitingRoomController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Patients
    Route::resource('patients', PatientController::class);
    
    // Médecins
    Route::resource('doctors', DoctorController::class);
    
    // Rendez-vous
    Route::resource('appointments', AppointmentController::class);
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
    Route::get('/api/events', [AppointmentController::class, 'getEvents'])->name('api.events');
    
    // Salle d'attente
    Route::get('/waiting-room', [WaitingRoomController::class, 'index'])->name('waiting-room');
    Route::post('/waiting-room/add', [WaitingRoomController::class, 'add'])->name('waiting-room.add');
    Route::post('/waiting-room/{waitingRoom}/start', [WaitingRoomController::class, 'startConsultation'])->name('waiting-room.start');
    Route::post('/waiting-room/{waitingRoom}/complete', [WaitingRoomController::class, 'complete'])->name('waiting-room.complete');
    Route::delete('/waiting-room/{waitingRoom}/remove', [WaitingRoomController::class, 'remove'])->name('waiting-room.remove');
    
    // Ordonnances
    Route::resource('prescriptions', PrescriptionController::class);
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');
    
    // Consultations
    Route::resource('consultations', ConsultationController::class);
    
    // Factures
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'addPayment'])->name('invoices.pay');
    
    // Exports
    Route::get('/export/patients', [ExportController::class, 'patients'])->name('export.patients');
    Route::get('/export/appointments', [ExportController::class, 'appointments'])->name('export.appointments');
    
    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/yearly', [ReportController::class, 'yearly'])->name('reports.yearly');
    
    // Gestion utilisateurs (admin uniquement)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});