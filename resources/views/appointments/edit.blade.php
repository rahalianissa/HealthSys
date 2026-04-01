@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-warning">
            <h4 class="mb-0"><i class="fas fa-edit"></i> Modifier rendez-vous</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-control" required>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Médecin <span class="text-danger">*</span></label>
                        <select name="doctor_id" class="form-control" required>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date et heure <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_time" class="form-control" value="{{ \Carbon\Carbon::parse($appointment->date_time)->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Durée (minutes)</label>
                        <input type="number" name="duration" class="form-control" value="{{ $appointment->duration }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de consultation</label>
                        <select name="type" class="form-control">
                            <option value="general" {{ $appointment->type == 'general' ? 'selected' : '' }}>Générale</option>
                            <option value="emergency" {{ $appointment->type == 'emergency' ? 'selected' : '' }}>Urgence</option>
                            <option value="follow_up" {{ $appointment->type == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                            <option value="specialist" {{ $appointment->type == 'specialist' ? 'selected' : '' }}>Spécialiste</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Motif</label>
                        <textarea name="reason" class="form-control" rows="2">{{ $appointment->reason }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $appointment->notes }}</textarea>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection