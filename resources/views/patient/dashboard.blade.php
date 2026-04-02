@extends('layouts.app')

@section('title', 'Mon espace patient')
@section('page-title', 'Tableau de bord patient')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="welcome-card">
            <h2 class="mb-0">BIENVENUE</h2>
            <p>à votre espace patient</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                <h3>{{ \App\Models\Appointment::where('patient_id', auth()->user()->patient->id)->where('status', 'confirmed')->where('date_time', '>', now())->count() }}</h3>
                <p class="text-muted mb-0">Prochains rendez-vous</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                <h3>{{ \App\Models\Prescription::where('patient_id', auth()->user()->patient->id)->count() }}</h3>
                <p class="text-muted mb-0">Ordonnances</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-file-invoice-dollar fa-3x text-warning mb-3"></i>
                <h3>{{ \App\Models\Invoice::where('patient_id', auth()->user()->patient->id)->where('status', 'pending')->count() }}</h3>
                <p class="text-muted mb-0">Factures impayées</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-stethoscope fa-3x text-info mb-3"></i>
                <h3>{{ \App\Models\Consultation::where('patient_id', auth()->user()->patient->id)->count() }}</h3>
                <p class="text-muted mb-0">Consultations</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('patient.appointments') }}" class="btn btn-custom">
                        <i class="fas fa-calendar-plus"></i> Prendre un rendez-vous
                    </a>
                    <a href="{{ route('patient.medical-record') }}" class="btn btn-outline-primary">
                        <i class="fas fa-folder-open"></i> Voir mon dossier médical
                    </a>
                    <a href="{{ route('patient.prescriptions') }}" class="btn btn-outline-success">
                        <i class="fas fa-prescription"></i> Mes ordonnances
                    </a>
                    <a href="{{ route('patient.invoices') }}" class="btn btn-outline-warning">
                        <i class="fas fa-file-invoice-dollar"></i> Mes factures
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Prochains rendez-vous</h5>
            </div>
            <div class="card-body">
                @php
                    $appointments = \App\Models\Appointment::with(['doctor.user'])
                        ->where('patient_id', auth()->user()->patient->id)
                        ->where('date_time', '>', now())
                        ->where('status', 'confirmed')
                        ->orderBy('date_time')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($appointments->count() > 0)
                    <div class="list-group">
                        @foreach($appointments as $appointment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Dr. {{ $appointment->doctor->user->name }}</strong>
                                        <br>
                                        <small>{{ $appointment->date_time->format('d/m/Y à H:i') }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $appointment->type }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Aucun rendez-vous à venir</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection