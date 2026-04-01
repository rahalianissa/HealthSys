@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-tachometer-alt text-primary"></i> Tableau de bord
                </h5>
            </div>
            <div class="card-body">
                <h4>Bienvenue, {{ auth()->user()->name }}!</h4>
                <p class="text-muted">Bienvenue sur HealthSys - Système de gestion de cabinet médical.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Patients</h6>
                                        <h2 class="mb-0">{{ \App\Models\Patient::count() }}</h2>
                                    </div>
                                    <i class="fas fa-users fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection