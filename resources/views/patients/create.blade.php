@extends('layouts.app')

@section('title', 'Ajouter un patient')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-user-plus text-primary"></i> Ajouter un patient
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ url('/patients') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de naissance <span class="text-danger">*</span></label>
                    <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
                    @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Numéro mutuelle</label>
                    <input type="text" name="insurance_number" class="form-control" value="{{ old('insurance_number') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Compagnie mutuelle</label>
                    <input type="text" name="insurance_company" class="form-control" value="{{ old('insurance_company') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact d'urgence</label>
                    <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone urgence</label>
                    <input type="text" name="emergency_phone" class="form-control" value="{{ old('emergency_phone') }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Allergies</label>
                    <textarea name="allergies" class="form-control" rows="2" placeholder="Liste des allergies...">{{ old('allergies') }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="{{ url('/patients') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection