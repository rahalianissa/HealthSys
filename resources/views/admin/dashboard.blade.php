@extends('layouts.app')

@section('title', 'Espace chef de médecine')
@section('page-title', 'Tableau de bord administration')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="welcome-card">
            <h2 class="mb-0">BIENVENUE</h2>
            <p>à votre espace chef de médecine</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                <h3>{{ \App\Models\User::where('role', 'doctor')->count() }}</h3>
                <p class="text-muted mb-0">Médecins</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-user-tie fa-3x text-success mb-3"></i>
                <h3>{{ \App\Models\User::where('role', 'secretaire')->count() }}</h3>
                <p class="text-muted mb-0">Secrétaires</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-warning mb-3"></i>
                <h3>{{ \App\Models\Patient::count() }}</h3>
                <p class="text-muted mb-0">Patients</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stats text-center">
            <div class="card-body">
                <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                <h3>{{ number_format(\App\Models\Invoice::sum('amount'), 0) }} DT</h3>
                <p class="text-muted mb-0">Chiffre d'affaires</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Gestion</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-custom">
                        <i class="fas fa-user-md"></i> Gérer les médecins
                    </a>
                    <a href="{{ route('admin.secretaries.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-tie"></i> Gérer les secrétaires
                    </a>
                    <a href="{{ route('admin.specialites.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-tag"></i> Gérer les spécialités
                    </a>
                    <a href="{{ route('admin.departements.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-building"></i> Gérer les départements
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-warning">
                        <i class="fas fa-chart-bar"></i> Rapports et analyses
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Statistiques mensuelles</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Derniers médecins ajoutés</h5>
            </div>
            <div class="card-body">
                @php
                    $recentDoctors = \App\Models\User::with('specialite')
                        ->where('role', 'doctor')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($recentDoctors->count() > 0)
                    <div class="list-group">
                        @foreach($recentDoctors as $doctor)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Dr. {{ $doctor->name }}</strong>
                                        <br>
                                        <small>{{ $doctor->specialite->nom ?? 'Spécialité non définie' }}</small>
                                    </div>
                                    <small class="text-muted">{{ $doctor->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Aucun médecin enregistré</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Derniers patients</h5>
            </div>
            <div class="card-body">
                @php
                    $recentPatients = \App\Models\Patient::with('user')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($recentPatients->count() > 0)
                    <div class="list-group">
                        @foreach($recentPatients as $patient)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $patient->user->name }}</strong>
                                        <br>
                                        <small>{{ $patient->user->email }}</small>
                                    </div>
                                    <small class="text-muted">{{ $patient->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Aucun patient enregistré</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Rendez-vous',
                data: [
                    {{ \App\Models\Appointment::whereMonth('date_time', 1)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 2)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 3)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 4)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 5)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 6)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 7)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 8)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 9)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 10)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 11)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 12)->count() }}
                ],
                backgroundColor: '#1a5f7a'
            }]
        }
    });
</script>
@endsection