@extends('layouts.app')

@section('title', 'Mon espace patient')
@section('page-title', 'Tableau de bord patient')

@section('content')


@php
    $patient = auth()->user()->patient;
    $patientId = $patient ? $patient->id : null;
@endphp

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                <h3>
                    @if($patientId)
                        {{ \App\Models\Appointment::where('patient_id', $patientId)->where('status', 'confirmed')->where('date_time', '>', now())->count() }}
                    @else
                        0
                    @endif
                </h3>
                <p class="text-muted mb-0">Prochains rendez-vous</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                <h3>
                    @if($patientId)
                        {{ \App\Models\Prescription::where('patient_id', $patientId)->count() }}
                    @else
                        0
                    @endif
                </h3>
                <p class="text-muted mb-0">Ordonnances</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-file-invoice-dollar fa-3x text-warning mb-3"></i>
                <h3>
                    @if($patientId)
                        {{ \App\Models\Invoice::where('patient_id', $patientId)->where('status', 'pending')->count() }}
                    @else
                        0
                    @endif
                </h3>
                <p class="text-muted mb-0">Factures impayées</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-stethoscope fa-3x text-info mb-3"></i>
                <h3>
                    @if($patientId)
                        {{ \App\Models\Consultation::where('patient_id', $patientId)->count() }}
                    @else
                        0
                    @endif
                </h3>
                <p class="text-muted mb-0">Consultations</p>
            </div>
        </div>
    </div>
</div>



<div class="row">
    {{-- Upcoming Appointments --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Prochains rendez-vous</h5>
                <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body">
                @php
                    $appointments = $patientId ? \App\Models\Appointment::with(['doctor.user'])
                        ->where('patient_id', $patientId)
                        ->where('date_time', '>', now())
                        ->where('status', 'confirmed')
                        ->orderBy('date_time')
                        ->limit(5)
                        ->get() : collect();
                @endphp
                
                @if($appointments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($appointments as $appointment)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="fas fa-user-md text-primary"></i>
                                            </div>
                                            <strong>Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="ms-4">
                                            <small class="text-muted d-block">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ \Carbon\Carbon::parse($appointment->date_time)->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="far fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($appointment->date_time)->format('H:i') }}
                                            </small>
                                            @if($appointment->reason)
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-sticky-note me-1"></i>
                                                {{ $appointment->reason }}
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $appointment->type ?? 'Consultation' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="far fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Aucun rendez-vous à venir</p>
                        <a href="#" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Prendre un rendez-vous
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Consultations --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-stethoscope me-2 text-info"></i>Dernières consultations</h5>
                <a href="#" class="btn btn-sm btn-outline-info">Voir tout</a>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Current Medications --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-pills me-2 text-success"></i>Traitements en cours</h5>
                <a href="{{ route('patient.prescriptions') }}" class="btn btn-sm btn-outline-success">Voir tout</a>
            </div>
          
        </div>
    </div>

    {{-- Lab Results --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-flask me-2 text-warning"></i>Derniers résultats d'analyses</h5>
                <a href="#" class="btn btn-sm btn-outline-warning">Voir tout</a>
            </div>
            <div class="card-body">
                
            </div>
        </div>
    </div>
</div>
@endsection