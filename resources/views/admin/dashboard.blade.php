@extends('layouts.app')

@section('title', 'Bienvenue')
@section('page-title', 'à votre espace chef de médecine')

@section('content')

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Doctors -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Médecins</h6>
                        <h3 class="fw-bold mb-0">{{ \App\Models\User::where('role', 'doctor')->count() }}</h3>
                    </div>
                    <i class="fas fa-user-doctor fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Secretaries -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Secrétaires</h6>
                        <h3 class="fw-bold mb-0">{{ \App\Models\User::where('role', 'secretaire')->count() }}</h3>
                    </div>
                    <i class="fas fa-user-tie fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Patients -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Patients</h6>
                        <h3 class="fw-bold mb-0">{{ \App\Models\Patient::count() }}</h3>
                    </div>
                    <i class="fas fa-user-injured fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Chiffre d'affaires</h6>
                        <h3 class="fw-bold mb-0">{{ number_format(\App\Models\Invoice::sum('amount') ?? 0, 0) }} DT</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        
        <!-- User Distribution Chart (Left) -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-info"></i>Répartition des utilisateurs
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 250px;">
                    <canvas id="userDistributionChart"></canvas>
                </div>
                <div class="card-footer bg-white border-0 py-2">
                    <div class="d-flex justify-content-center gap-3 small flex-wrap">
                        <span><i class="fas fa-circle text-primary me-1"></i> Médecins</span>
                        <span><i class="fas fa-circle text-success me-1"></i> Secrétaires</span>
                        <span><i class="fas fa-circle text-warning me-1"></i> Patients</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Chart (Right) -->
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-info"></i>Statistiques mensuelles</h5>
                    <span class="badge bg-light text-dark">{{ date('Y') }}</span>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Lists Row -->
    <div class="row g-4 mt-2">
        
        <!-- Recent Doctors -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0"><i class="fas fa-user-doctor me-2 text-primary"></i>Derniers médecins ajoutés</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentDoctors = \App\Models\User::with('specialite')
                            ->where('role', 'doctor')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($recentDoctors->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($recentDoctors as $doctor)
                                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Dr. {{ $doctor->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $doctor->specialite->nom ?? 'Spécialité non définie' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-light text-dark">
                                            {{ $doctor->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="fas fa-info-circle me-2"></i>Aucun médecin enregistré
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Patients -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0"><i class="fas fa-user-injured me-2 text-warning"></i>Derniers patients</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentPatients = \App\Models\Patient::with('user')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($recentPatients->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($recentPatients as $patient)
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $patient->user->name ?? 'Patient' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $patient->user->email ?? 'Email non disponible' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-light text-dark">
                                            {{ $patient->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="fas fa-info-circle me-2"></i>Aucun patient enregistré
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== USER DISTRIBUTION CHART (Doughnut) =====
    const userCtx = document.getElementById('userDistributionChart');
    if (userCtx) {
        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Médecins', 'Secrétaires', 'Patients'],
                datasets: [{
                    data: [
                        {{ \App\Models\User::where('role', 'doctor')->count() }},
                        {{ \App\Models\User::where('role', 'secretaire')->count() }},
                        {{ \App\Models\Patient::count() }}
                    ],
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.85)',
                        'rgba(25, 135, 84, 0.85)',
                        'rgba(255, 193, 7, 0.85)'
                    ],
                    borderColor: [
                        'rgba(13, 110, 253, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.2)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 800
                }
            }
        });
    }

    // ===== MONTHLY APPOINTMENTS CHART (Bar) =====
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Rendez-vous',
                    data: [
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 1)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 2)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 3)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 4)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 5)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 6)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 7)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 8)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 9)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 10)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 11)->count() }},
                        {{ \App\Models\Appointment::whereYear('date_time', date('Y'))->whereMonth('date_time', 12)->count() }}
                    ],
                    backgroundColor: 'rgba(26, 95, 122, 0.85)',
                    borderColor: 'rgba(26, 95, 122, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 28,
                    maxBarThickness: 35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.2)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.parsed.y} rendez-vous`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#6c757d',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            borderDash: [4, 4]
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 }
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }
});
</script>
@endpush