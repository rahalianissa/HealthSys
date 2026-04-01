@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-calendar-alt"></i> Liste des rendez-vous</h4>
            <a href="{{ route('appointments.create') }}" class="btn btn-light">
                <i class="fas fa-plus"></i> Nouveau rendez-vous
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            几十年
                                <th>#</th>
                                <th>Patient</th>
                                <th>Médecin</th>
                                <th>Date et heure</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $appointment->patient->user->name }}</strong><br>
                                    <small>{{ $appointment->patient->user->phone }}</small>
                                </td>
                                <td>Dr. {{ $appointment->doctor->user->name }}<br>
                                    <small>{{ $appointment->doctor->specialty }}</small>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($appointment->date_time)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @if($appointment->type == 'general')
                                        <span class="badge bg-info">Générale</span>
                                    @elseif($appointment->type == 'emergency')
                                        <span class="badge bg-danger">Urgence</span>
                                    @elseif($appointment->type == 'follow_up')
                                        <span class="badge bg-warning">Suivi</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $appointment->type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appointment->status == 'pending')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($appointment->status == 'confirmed')
                                        <span class="badge bg-success">Confirmé</span>
                                    @elseif($appointment->status == 'cancelled')
                                        <span class="badge bg-danger">Annulé</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $appointment->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce rendez-vous ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <p>Aucun rendez-vous enregistré.</p>
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">Prendre un rendez-vous</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection