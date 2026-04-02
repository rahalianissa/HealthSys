<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\WaitingRoomController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\ComptabiliteController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

require __DIR__.'/auth.php';

// Routes pour tous les utilisateurs authentifiés
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role == 'patient') {
            return view('patient.dashboard');
        } elseif ($user->role == 'doctor') {
            return view('doctor.dashboard');
        } elseif ($user->role == 'secretaire') {
            return view('secretaire.dashboard');
        } elseif ($user->role == 'chef_medecine') {
            return view('admin.dashboard');
        }
        
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Routes pour les patients
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'patientIndex'])->name('appointments');
    Route::post('/appointments/book', [AppointmentController::class, 'bookOnline'])->name('book');
    Route::post('/appointments/cancel/{id}', [AppointmentController::class, 'cancelOnline'])->name('cancel');
    Route::get('/medical-record', [ConsultationController::class, 'patientMedicalRecord'])->name('medical-record');
    Route::get('/prescriptions', [PrescriptionController::class, 'patientPrescriptions'])->name('prescriptions');
    Route::get('/invoices', [InvoiceController::class, 'patientInvoices'])->name('invoices');
});

// Routes pour les médecins
Route::middleware(['auth', 'role:doctor,chef_medecine'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/waiting-room', [WaitingRoomController::class, 'doctorIndex'])->name('waiting-room');
    Route::post('/consultation/start/{waitingRoom}', [WaitingRoomController::class, 'startConsultation'])->name('consultation.start');
    Route::get('/consultations', [ConsultationController::class, 'doctorConsultations'])->name('consultations');
    Route::get('/history', [ConsultationController::class, 'visitHistory'])->name('history');
    Route::get('/establish-document', [DocumentController::class, 'establish'])->name('establish-document');
    Route::post('/establish-document/prescription', [DocumentController::class, 'storePrescription'])->name('store-prescription');
    Route::post('/establish-document/certificate', [DocumentController::class, 'storeCertificate'])->name('store-certificate');
    Route::post('/establish-document/report', [DocumentController::class, 'storeReport'])->name('store-report');
});

// Routes pour les secrétaires
Route::middleware(['auth', 'role:secretaire,chef_medecine'])->prefix('secretaire')->name('secretaire.')->group(function () {
    Route::get('/comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite');
    Route::get('/paiements', [ComptabiliteController::class, 'paiements'])->name('paiements');
    Route::get('/facture/create', [ComptabiliteController::class, 'createFacture'])->name('facture.create');
    Route::post('/facture', [ComptabiliteController::class, 'storeFacture'])->name('facture.store');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents');
    Route::get('/waiting-room', [WaitingRoomController::class, 'secretaireIndex'])->name('waiting-room');
    Route::post('/waiting-room/add', [WaitingRoomController::class, 'add'])->name('waiting-room.add');
    Route::delete('/waiting-room/{waitingRoom}', [WaitingRoomController::class, 'remove'])->name('waiting-room.remove');
});

// Routes pour le chef de médecine (admin)
Route::middleware(['auth', 'role:chef_medecine'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Gestion des médecins
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    
    // Gestion des secrétaires
    Route::get('/secretaries', [SecretaryController::class, 'index'])->name('secretaries.index');
    Route::get('/secretaries/create', [SecretaryController::class, 'create'])->name('secretaries.create');
    Route::post('/secretaries', [SecretaryController::class, 'store'])->name('secretaries.store');
    Route::get('/secretaries/{secretary}/edit', [SecretaryController::class, 'edit'])->name('secretaries.edit');
    Route::put('/secretaries/{secretary}', [SecretaryController::class, 'update'])->name('secretaries.update');
    Route::delete('/secretaries/{secretary}', [SecretaryController::class, 'destroy'])->name('secretaries.destroy');
    
    // Gestion des spécialités
    Route::get('/specialites', [SpecialiteController::class, 'index'])->name('specialites.index');
    Route::get('/specialites/create', [SpecialiteController::class, 'create'])->name('specialites.create');
    Route::post('/specialites', [SpecialiteController::class, 'store'])->name('specialites.store');
    Route::get('/specialites/{specialite}/edit', [SpecialiteController::class, 'edit'])->name('specialites.edit');
    Route::put('/specialites/{specialite}', [SpecialiteController::class, 'update'])->name('specialites.update');
    Route::delete('/specialites/{specialite}', [SpecialiteController::class, 'destroy'])->name('specialites.destroy');
    
    // Gestion des départements
    Route::get('/departements', [DepartementController::class, 'index'])->name('departements.index');
    Route::get('/departements/create', [DepartementController::class, 'create'])->name('departements.create');
    Route::post('/departements', [DepartementController::class, 'store'])->name('departements.store');
    Route::get('/departements/{departement}/edit', [DepartementController::class, 'edit'])->name('departements.edit');
    Route::put('/departements/{departement}', [DepartementController::class, 'update'])->name('departements.update');
    Route::delete('/departements/{departement}', [DepartementController::class, 'destroy'])->name('departements.destroy');
    
    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/yearly', [ReportController::class, 'yearly'])->name('reports.yearly');
    
    // Exports
    Route::get('/export/patients', [ExportController::class, 'patients'])->name('export.patients');
    Route::get('/export/appointments', [ExportController::class, 'appointments'])->name('export.appointments');
    Route::get('/export/invoices', [ExportController::class, 'invoices'])->name('export.invoices');
});

// Routes publiques pour les consultations (API)
Route::get('/consultations/{id}/details', [ConsultationController::class, 'details'])->name('consultations.details');
Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');