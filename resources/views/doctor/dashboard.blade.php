@extends('layouts.app')

@section('title', 'Espace médecin')
@section('page-title', 'Tableau de bord médecin')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="welcome-card">
            <h2 class="mb-0">BIENVENUE</h2>
            <p>à votre espace médecin</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                <h3>{{ \App\Models\WaitingRoom::where('doctor_id', auth()->user()->doctor->id)->where('status', 'waiting')->count() }}</h3>
                <p class="text-muted mb-0">Patients en attente</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                <h3>{{ \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id)->where('date_time', '>', now())->where('status', 'confirmed')->count() }}</h3>
                <p class="text-muted mb-0">Rendez-vous aujourd'hui</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-history fa-3x text-warning mb-3"></i>
                <h3>{{ \App\Models\Consultation::where('doctor_id', auth()->user()->doctor->id)->count() }}</h3>
                <p class="text-muted mb-0">Consultations effectuées</p>
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
                    <a href="{{ route('doctor.waiting-room') }}" class="btn btn-custom">
                        <i class="fas fa-clock"></i> Salle d'attente
                    </a>
                    <a href="{{ route('doctor.consultations') }}" class="btn btn-outline-primary">
                        <i class="fas fa-stethoscope"></i> Mes consultations
                    </a>
                    <a href="{{ route('doctor.history') }}" class="btn btn-outline-info">
                        <i class="fas fa-history"></i> Historique des visites
                    </a>
                    <a href="{{ route('doctor.establish-document') }}" class="btn btn-outline-success">
                        <i class="fas fa-file-alt"></i> Établir un document
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Rendez-vous du jour</h5>
            </div>
            <div class="card-body">
                @php
                    $todayAppointments = \App\Models\Appointment::with(['patient.user'])
                        ->where('doctor_id', auth()->user()->doctor->id)
                        ->whereDate('date_time', today())
                        ->orderBy('date_time')
                        ->get();
                @endphp
                
                @if($todayAppointments->count() > 0)
                    <div class="list-group">
                        @foreach($todayAppointments as $appointment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $appointment->patient->user->name }}</strong>
                                        <br>
                                        <small>{{ $appointment->date_time->format('H:i') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $appointment->status == 'confirmed' ? 'success' : 'warning' }}">
                                        {{ $appointment->status == 'confirmed' ? 'Confirmé' : 'En attente' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Aucun rendez-vous aujourd'hui</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection