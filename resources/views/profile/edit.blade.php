@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <i class="fas fa-user-circle fa-4x"></i>
                    <h4 class="mt-2">{{ auth()->user()->name }}</h4>
                </div>
                <div class="card-body text-center">
                    <p><strong>Rôle:</strong> {{ ucfirst(auth()->user()->role) }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Membre depuis:</strong> {{ auth()->user()->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Modifier mes informations</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nom complet</label>
                                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Téléphone</label>
                                <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Date de naissance</label>
                                <input type="date" name="birth_date" class="form-control" value="{{ auth()->user()->birth_date }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Adresse</label>
                                <textarea name="address" class="form-control" rows="2">{{ auth()->user()->address }}</textarea>
                            </div>
                        </div>
                        
                        <hr>
                        <h5>Changer le mot de passe</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nouveau mot de passe</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection