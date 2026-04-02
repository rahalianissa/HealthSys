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
    
    // ==================== PATIENTS ====================
    Route::resource('patients', PatientController::class);
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients/export/pdf', [PatientController::class, 'exportPdf'])->name('patients.export.pdf');
    Route::get('/patients/export/excel', [PatientController::class, 'exportExcel'])->name('patients.export.excel');
    Route::get('/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])->name('patients.medical-history');
    Route::get('/patients/{patient}/appointments', [PatientController::class, 'appointments'])->name('patients.appointments');
    Route::get('/patients/{patient}/invoices', [PatientController::class, 'invoices'])->name('patients.invoices');
    
    // ==================== MÉDECINS ====================
    Route::resource('doctors', DoctorController::class);
    Route::get('/doctors/{doctor}/schedule', [DoctorController::class, 'schedule'])->name('doctors.schedule');
    Route::get('/doctors/export/pdf', [DoctorController::class, 'exportPdf'])->name('doctors.export.pdf');
    
    // ==================== RENDEZ-VOUS ====================
    Route::resource('appointments', AppointmentController::class);
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
    Route::get('/api/events', [AppointmentController::class, 'getEvents'])->name('api.events');
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'changeStatus'])->name('appointments.status');
    
    // ==================== SALLE D'ATTENTE ====================
    Route::get('/waiting-room', [WaitingRoomController::class, 'index'])->name('waiting-room');
    Route::post('/waiting-room/add', [WaitingRoomController::class, 'add'])->name('waiting-room.add');
    Route::post('/waiting-room/{waitingRoom}/start', [WaitingRoomController::class, 'startConsultation'])->name('waiting-room.start');
    Route::post('/waiting-room/{waitingRoom}/complete', [WaitingRoomController::class, 'complete'])->name('waiting-room.complete');
    Route::delete('/waiting-room/{waitingRoom}/remove', [WaitingRoomController::class, 'remove'])->name('waiting-room.remove');
    Route::post('/waiting-room/{waitingRoom}/priority', [WaitingRoomController::class, 'updatePriority'])->name('waiting-room.priority');
    Route::get('/waiting-room/queue', [WaitingRoomController::class, 'getQueue'])->name('waiting-room.queue');
    
    // ==================== ORDONNANCES ====================
    Route::resource('prescriptions', PrescriptionController::class);
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');
    Route::get('/prescriptions/patient/{patient}', [PrescriptionController::class, 'forPatient'])->name('prescriptions.patient');
    Route::post('/prescriptions/{prescription}/renew', [PrescriptionController::class, 'renew'])->name('prescriptions.renew');
    
    // ==================== CONSULTATIONS ====================
    Route::resource('consultations', ConsultationController::class);
    Route::get('/consultations/patient/{patient}', [ConsultationController::class, 'forPatient'])->name('consultations.patient');
    Route::get('/consultations/doctor/{doctor}', [ConsultationController::class, 'forDoctor'])->name('consultations.doctor');
    
    // ==================== FACTURES ====================
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'addPayment'])->name('invoices.pay');
    
    // ==================== EXPORTS ====================
    Route::get('/export/patients', [ExportController::class, 'patients'])->name('export.patients');
    Route::get('/export/appointments', [ExportController::class, 'appointments'])->name('export.appointments');
    
    // ==================== RAPPORTS ====================
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/yearly', [ReportController::class, 'yearly'])->name('reports.yearly');
    
    // ==================== GESTION UTILISATEURS (Admin uniquement) ====================
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});