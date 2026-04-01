@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-warning">
            <h4 class="mb-0"><i class="fas fa-edit"></i> Modifier patient</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('patients.update', $patient) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $patient->user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $patient->user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $patient->user->phone) }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de naissance <span class="text-danger">*</span></label>
                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $patient->user->birth_date) }}" required>
                        @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Adresse</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $patient->user->address) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Numéro mutuelle</label>
                        <input type="text" name="insurance_number" class="form-control" value="{{ old('insurance_number', $patient->insurance_number) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Compagnie mutuelle</label>
                        <input type="text" name="insurance_company" class="form-control" value="{{ old('insurance_company', $patient->insurance_company) }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Allergies</label>
                        <textarea name="allergies" class="form-control" rows="2">{{ old('allergies', $patient->allergies) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact d'urgence</label>
                        <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $patient->emergency_contact) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone urgence</label>
                        <input type="text" name="emergency_phone" class="form-control" value="{{ old('emergency_phone', $patient->emergency_phone) }}">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection