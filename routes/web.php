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
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\ComptabiliteController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\SecretaryDashboardController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

require __DIR__.'/auth.php';

// Routes pour tous les utilisateurs authentifiés
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match($user->role) {
            'patient' => redirect()->route('patient.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'secretaire' => redirect()->route('secretaire.dashboard'),
            'chef_medecine' => redirect()->route('admin.dashboard'),
            default => view('dashboard'),
        };
    })->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// API Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/api/events', function () {
        $appointments = App\Models\Appointment::with(['patient.user', 'doctor.user'])->get();
        return response()->json($appointments->map(fn($a) => [
            'id' => $a->id,
            'title' => $a->patient->user->name . ' - Dr. ' . $a->doctor->user->name,
            'start' => $a->date_time->toIso8601String(),
            'end' => $a->date_time->copy()->addMinutes($a->duration)->toIso8601String(),
            'color' => match($a->status) { 'confirmed' => '#28a745', 'cancelled' => '#dc3545', 'completed' => '#6c757d', default => '#ffc107' },
        ]));
    })->name('api.events');
    
    Route::get('/consultations/{consultation}/details', [ConsultationController::class, 'details'])->name('consultations.details');
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
});

// Routes Patients
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [AppointmentController::class, 'patientIndex'])->name('appointments');
    Route::post('/book', [AppointmentController::class, 'bookOnline'])->name('book');
    Route::post('/appointments/cancel/{id}', [AppointmentController::class, 'cancelOnline'])->name('appointments.cancel');
    Route::get('/medical-record', [ConsultationController::class, 'patientMedicalRecord'])->name('medical-record');
    Route::get('/prescriptions', [PrescriptionController::class, 'patientPrescriptions'])->name('prescriptions');
    Route::get('/invoices', [InvoiceController::class, 'patientInvoices'])->name('invoices');
});

// Routes Médecins
Route::middleware(['auth', 'role:doctor,chef_medecine'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/waiting-room', [WaitingRoomController::class, 'doctorIndex'])->name('waiting-room');
    Route::post('/consultation/start/{waitingRoom}', [WaitingRoomController::class, 'startConsultation'])->name('consultation.start');
    Route::post('/consultation/complete/{waitingRoom}', [WaitingRoomController::class, 'complete'])->name('consultation.complete');
    Route::get('/consultations', [ConsultationController::class, 'doctorConsultations'])->name('consultations');
    Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::get('/history', [ConsultationController::class, 'visitHistory'])->name('history');
    Route::get('/establish-document', [DocumentController::class, 'establish'])->name('establish-document');
    Route::post('/store-prescription', [DocumentController::class, 'storePrescription'])->name('store-prescription');
    Route::post('/store-certificate', [DocumentController::class, 'storeCertificate'])->name('store-certificate');
    Route::post('/store-report', [DocumentController::class, 'storeReport'])->name('store-report');
    Route::get('/patients', [DoctorController::class, 'myPatients'])->name('patients');
    Route::get('/patients/{patient}', [DoctorController::class, 'showPatient'])->name('patients.show');
    Route::get('/notifications', [DoctorController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/mark-all', [DoctorController::class, 'markAllNotifications'])->name('notifications.mark-all');
    Route::post('/notifications/{id}/mark-read', [DoctorController::class, 'markNotificationRead'])->name('notifications.read');
});

// Routes Secrétaires
Route::middleware(['auth', 'role:secretaire,chef_medecine'])->prefix('secretaire')->name('secretaire.')->group(function () {
    Route::get('/dashboard', [SecretaryDashboardController::class, 'index'])->name('dashboard');
    Route::get('/comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite');
    Route::get('/facture/create', [ComptabiliteController::class, 'createFacture'])->name('facture.create');
    Route::post('/facture', [ComptabiliteController::class, 'storeFacture'])->name('facture.store');
    Route::resource('appointments', AppointmentController::class);
    Route::get('/appointments/{appointment}/json', [AppointmentController::class, 'showJson'])->name('appointments.json');
    Route::resource('patients', PatientController::class);
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents');
    Route::get('/documents/search', [DocumentController::class, 'search'])->name('documents.search');
    Route::get('/waiting-room', [WaitingRoomController::class, 'secretaireIndex'])->name('waiting-room');
    Route::post('/waiting-room/add', [WaitingRoomController::class, 'add'])->name('waiting-room.add');
    Route::delete('/waiting-room/{waitingRoom}/remove', [WaitingRoomController::class, 'remove'])->name('waiting-room.remove');
});

// Routes Admin (Chef de médecine)
Route::middleware(['auth', 'role:chef_medecine'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('chart-data');
    Route::resource('doctors', DoctorController::class);
    Route::resource('secretaries', SecretaryController::class);
    Route::resource('specialites', SpecialiteController::class);
    Route::resource('departements', DepartementController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/yearly', [ReportController::class, 'yearly'])->name('reports.yearly');
    Route::get('/export/patients', [ExportController::class, 'patients'])->name('export.patients');
    Route::get('/export/appointments', [ExportController::class, 'appointments'])->name('export.appointments');
    Route::get('/export/invoices', [ExportController::class, 'invoices'])->name('export.invoices');
});

// Langue
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');