@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-check"></i> Détails du rendez-vous</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-user"></i> Patient</strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nom:</strong> {{ $appointment->patient->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $appointment->patient->user->email }}</p>
                                    <p><strong>Téléphone:</strong> {{ $appointment->patient->user->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-user-md"></i> Médecin</strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nom:</strong> Dr. {{ $appointment->doctor->user->name }}</p>
                                    <p><strong>Spécialité:</strong> {{ $appointment->doctor->specialty }}</p>
                                    <p><strong>Honoraire:</strong> {{ number_format($appointment->doctor->consultation_fee, 2) }} DT</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-info-circle"></i> Informations du rendez-vous</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>📅 Date:</strong> {{ \Carbon\Carbon::parse($appointment->date_time)->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>⏰ Heure:</strong> {{ \Carbon\Carbon::parse($appointment->date_time)->format('H:i') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>⏱️ Durée:</strong> {{ $appointment->duration }} minutes</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>📋 Type:</strong> 
                                        @if($appointment->type == 'general')
                                            <span class="badge bg-info">Générale</span>
                                        @elseif($appointment->type == 'emergency')
                                            <span class="badge bg-danger">Urgence</span>
                                        @elseif($appointment->type == 'follow_up')
                                            <span class="badge bg-warning">Suivi</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $appointment->type }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>📌 Statut:</strong>
                                        @if($appointment->status == 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($appointment->status == 'confirmed')
                                            <span class="badge bg-success">Confirmé</span>
                                        @elseif($appointment->status == 'cancelled')
                                            <span class="badge bg-danger">Annulé</span>
                                        @elseif($appointment->status == 'completed')
                                            <span class="badge bg-secondary">Terminé</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $appointment->status }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>🔔 Rappel:</strong> {{ $appointment->reminder_sent ? 'Envoyé' : 'Non envoyé' }}</p>
                                </div>
                                @if($appointment->reason)
                                <div class="col-md-12">
                                    <hr>
                                    <p><strong>💬 Motif:</strong> {{ $appointment->reason }}</p>
                                </div>
                                @endif
                                @if($appointment->notes)
                                <div class="col-md-12">
                                    <p><strong>📝 Notes:</strong> {{ $appointment->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex space-x-2">
                            @if($appointment->status == 'pending')
                                <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Confirmer
                                    </button>
                                </form>
                            @endif
                            
                            @if($appointment->status == 'confirmed')
                                <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check-double"></i> Terminer
                                    </button>
                                </form>
                            @endif
                            
                            @if($appointment->status != 'cancelled' && $appointment->status != 'completed')
                                <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler ce rendez-vous ?')">
                                        <i class="fas fa-times-circle"></i> Annuler
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="d-flex space-x-2">
                            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection