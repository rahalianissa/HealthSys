@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-user-circle"></i> Détails du patient</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nom complet:</strong> {{ $patient->user->name }}</p>
                    <p><strong>Email:</strong> {{ $patient->user->email }}</p>
                    <p><strong>Téléphone:</strong> {{ $patient->user->phone }}</p>
                    <p><strong>Date de naissance:</strong> {{ \Carbon\Carbon::parse($patient->user->birth_date)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Adresse:</strong> {{ $patient->user->address ?? 'Non renseignée' }}</p>
                    <p><strong>Mutuelle:</strong> {{ $patient->insurance_company ?? 'Aucune' }}</p>
                    <p><strong>Numéro mutuelle:</strong> {{ $patient->insurance_number ?? 'Non renseigné' }}</p>
                    <p><strong>Allergies:</strong> {{ $patient->allergies ?? 'Aucune' }}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Contact d'urgence:</strong> {{ $patient->emergency_contact ?? 'Non renseigné' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Téléphone urgence:</strong> {{ $patient->emergency_phone ?? 'Non renseigné' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning">Modifier</a>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>
@endsection