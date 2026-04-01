@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-tachometer-alt"></i> Tableau de bord</h4>
                </div>
                <div class="card-body">
                    <h4>Bienvenue, {{ auth()->user()->name }}!</h4>
                    <p class="text-muted">Bienvenue sur HealthSys - Système de gestion de cabinet médical.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-3">
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
        
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Médecins</h6>
                            <h2 class="mb-0">{{ \App\Models\Doctor::count() }}</h2>
                        </div>
                        <i class="fas fa-user-md fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Rendez-vous aujourd'hui</h6>
                            <h2 class="mb-0">{{ \App\Models\Appointment::whereDate('date_time', today())->count() }}</h2>
                        </div>
                        <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Rendez-vous ce mois</h6>
                            <h2 class="mb-0">{{ \App\Models\Appointment::whereMonth('date_time', now()->month)->count() }}</h2>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Prochains rendez-vous</h5>
                </div>
                <div class="card-body">
                    @php
                        $upcoming = \App\Models\Appointment::with(['patient.user', 'doctor.user'])
                            ->where('date_time', '>=', now())
                            ->where('status', 'confirmed')
                            ->orderBy('date_time')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($upcoming->count() > 0)
                        <div class="list-group">
                            @foreach($upcoming as $appointment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $appointment->patient->user->name }}</strong><br>
                                            <small>Dr. {{ $appointment->doctor->user->name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">{{ $appointment->date_time->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Aucun rendez-vous à venir</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Statistiques rapides</h5>
                </div>
                <div class="card-body">
                    <canvas id="statsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Rendez-vous',
                data: [
                    {{ \App\Models\Appointment::whereMonth('date_time', 1)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 2)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 3)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 4)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 5)->count() }},
                    {{ \App\Models\Appointment::whereMonth('date_time', 6)->count() }}
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection