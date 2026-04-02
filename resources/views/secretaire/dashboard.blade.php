@extends('layouts.app')

@section('title', 'Espace secrétaire')
@section('page-title', 'Tableau de bord secrétaire')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="welcome-card">
            <h2 class="mb-0">BIENVENUE</h2>
            <p>à votre espace secrétaire</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h3>{{ \App\Models\Patient::count() }}</h3>
                <p class="text-muted mb-0">Patients</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                <h3>{{ \App\Models\Appointment::whereDate('date_time', today())->count() }}</h3>
                <p class="text-muted mb-0">Rendez-vous aujourd'hui</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                <h3>{{ \App\Models\WaitingRoom::where('status', 'waiting')->count() }}</h3>
                <p class="text-muted mb-0">Salle d'attente</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                <h3>{{ number_format(\App\Models\Invoice::whereMonth('created_at', now()->month)->sum('amount'), 0) }} DT</h3>
                <p class="text-muted mb-0">Revenus ce mois</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Menu principal</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('secretaire.comptabilite') }}" class="btn btn-custom">
                        <i class="fas fa-chart-line"></i> Comptabilité
                    </a>
                    <a href="{{ route('secretaire.appointments.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt"></i> Rendez-vous
                    </a>
                    <a href="{{ route('secretaire.patients.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-users"></i> Patients
                    </a>
                    <a href="{{ route('secretaire.documents') }}" class="btn btn-outline-info">
                        <i class="fas fa-file-alt"></i> Documents
                    </a>
                    <a href="{{ route('secretaire.waiting-room') }}" class="btn btn-outline-warning">
                        <i class="fas fa-clock"></i> Salle d'attente
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Rendez-vous du jour</h5>
            </div>
            <div class="card-body">
                @php
                    $todayAppointments = \App\Models\Appointment::with(['patient.user', 'doctor.user'])
                        ->whereDate('date_time', today())
                        ->orderBy('date_time')
                        ->get();
                @endphp
                
                @if($todayAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Heure</th>
                                    <th>Patient</th>
                                    <th>Médecin</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->date_time->format('H:i') }}</td>
                                    <td>{{ $appointment->patient->user->name }}</td>
                                    <td>Dr. {{ $appointment->doctor->user->name }}</td>
                                    <td>
                                        @if($appointment->status == 'confirmed')
                                            <span class="badge bg-success">Confirmé</span>
                                        @elseif($appointment->status == 'cancelled')
                                            <span class="badge bg-danger">Annulé</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun rendez-vous aujourd'hui</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection