@extends('layouts.app')

@section('title', 'Espace médecin')
@section('page-title', 'Tableau de bord médecin')

@section('content')


<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-stats h-100 shadow-sm border-0">
            <div class="card-body text-center p-4">
                <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                <h2 class="fw-bold mb-1">
                    {{ \App\Models\WaitingRoom::where('doctor_id', auth()->user()->doctor->id)->where('status', 'waiting')->count() }}
                </h2>
                <p class="text-muted mb-0">Patients en attente</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-stats h-100 shadow-sm border-0 border-primary border-2">
            <div class="card-body text-center p-4">
                <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                <h2 class="fw-bold mb-1">
                    {{ \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id)
                        ->whereDate('date_time', today())
                        ->whereIn('status', ['confirmed', 'pending'])->count() }}
                </h2>
                <p class="text-muted mb-0">Rendez-vous aujourd'hui</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-stats h-100 shadow-sm border-0">
            <div class="card-body text-center p-4">
                <i class="fas fa-history fa-3x text-warning mb-3"></i>
                <h2 class="fw-bold mb-1">
                    {{ \App\Models\Consultation::where('doctor_id', auth()->user()->doctor->id)->count() }}
                </h2>
                <p class="text-muted mb-0">Consultations effectuées</p>
            </div>
        </div>
    </div>
</div>


<!-- Main Content Row -->
<div class="row g-4">
    <!-- Today's Schedule -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Programme du jour</h5>
                <span class="badge bg-primary rounded-pill">
                    {{ \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id)->whereDate('date_time', today())->count() }}
                </span>
            </div>
            <div class="card-body">
                @php
                    $todayAppointments = \App\Models\Appointment::with(['patient.user'])
                        ->where('doctor_id', auth()->user()->doctor->id)
                        ->whereDate('date_time', today())
                        ->orderBy('date_time')
                        ->get();
                @endphp
                
                @if($todayAppointments->count() > 0)
                    <div class="timeline">
                        @foreach($todayAppointments as $appointment)
                            <div class="d-flex mb-4 pb-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle text-center p-2" style="width: 60px; height: 60px; line-height: 56px;">
                                        <strong>{{ $appointment->date_time->format('H') }}h</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $appointment->patient->user->name }}</h6>
                                            <p class="text-muted mb-1 small">
                                                <i class="fas fa-stethoscope me-1"></i>
                                                {{ $appointment->type ?? 'Consultation' }} - 
                                                {{ $appointment->duration ?? 30 }} min
                                            </p>
                                            @if($appointment->reason)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-info-circle me-1"></i>{{ $appointment->reason }}
                                                </small>
                                            @endif
                                        </div>
                                        <div>
                                            @if($appointment->status == 'confirmed')
                                                <span class="badge bg-success">Confirmé</span>
                                            @elseif($appointment->status == 'pending')
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            @elseif($appointment->status == 'completed')
                                                <span class="badge bg-secondary">Terminé</span>
                                            @else
                                                <span class="badge bg-{{ $appointment->status == 'cancelled' ? 'danger' : 'info' }}">
                                                    {{ $appointment->status }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        @if($appointment->status != 'completed' && $appointment->status != 'cancelled')
                                            <a href="{{ route('doctor.consultations.create', ['patient' => $appointment->patient->id, 'appointment' => $appointment->id]) }}" 
                                               class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-play me-1"></i> Consulter
                                            </a>
                                        @endif
                                        <a href="{{ route('doctor.patients.show', $appointment->patient->id) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-user me-1"></i> Dossier
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-day fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun rendez-vous aujourd'hui</h5>
                        <p class="text-muted mb-3">Profitez de cette journée pour rattraper votre retard administratif</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-calendar me-2"></i>Voir l'agenda
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Patients -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Patients récents</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body">
                @php
                    $recentPatients = \App\Models\Consultation::with('patient.user')
                        ->where('doctor_id', auth()->user()->doctor->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->unique('patient_id');
                @endphp
                
                @if($recentPatients->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentPatients as $consultation)
                            <div class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 45px; height: 45px;">
                                                <span class="text-primary fw-bold">
                                                    {{ substr($consultation->patient->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $consultation->patient->user->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $consultation->created_at->format('d/m/Y à H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('doctor.patients.show', $consultation->patient->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-injured fa-4x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Aucun patient récent</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Activity Chart
    const ctx = document.getElementById('weeklyActivityChart');
    if (ctx) {
        const doctorId = {{ auth()->user()->doctor->id }};
        
        // Fetch data from API or calculate directly
        const weeklyData = {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Consultations',
                data: [
                    @php
                        for ($i = 1; $i <= 7; $i++) {
                            $day = now()->startOfWeek()->addDays($i - 1);
                            $count = \App\Models\Consultation::where('doctor_id', auth()->user()->doctor->id)
                                ->whereDate('created_at', $day)->count();
                            echo $count;
                            if ($i < 7) echo ',';
                        }
                    @endphp
                ],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        };
        
        new Chart(ctx, {
            type: 'line',
            data: weeklyData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(33, 37, 41, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.2)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' consultation' + (context.parsed.y > 1 ? 's' : '');
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
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: '#495057',
                            font: { size: 11, weight: '600' }
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
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

@push('styles')
<style>
.card-stats {
    transition: transform 0.2s ease-in-out;
}
.card-stats:hover {
    transform: translateY(-5px);
}
.timeline {
    position: relative;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.avatar {
    font-size: 1.1rem;
}
.btn:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease-in-out;
}
</style>
@endpush