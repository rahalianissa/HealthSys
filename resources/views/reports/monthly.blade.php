@extends('layouts.app')

@section('title', 'Rapport mensuel')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rapport mensuel</h1>
            <p class="text-gray-500 mt-1">{{ $stats['month'] }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card">
            <div class="card-body">
                <p class="text-gray-500 text-sm">Rendez-vous</p>
                <p class="text-2xl font-bold">{{ $stats['appointments_count'] }}</p>
                <div class="mt-2 text-sm">
                    <span class="text-green-600">Confirmés: {{ $stats['confirmed_appointments'] }}</span><br>
                    <span class="text-red-600">Annulés: {{ $stats['cancelled_appointments'] }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-gray-500 text-sm">Nouveaux patients</p>
                <p class="text-2xl font-bold">{{ $stats['new_patients'] }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_revenue'], 2) }} DT</p>
                <p class="text-sm text-green-600">Payé: {{ number_format($stats['total_paid'], 2) }} DT</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-gray-500 text-sm">Impayés</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($stats['pending_payment'], 2) }} DT</p>
            </div>
        </div>
    </div>

    <!-- Graphique par type -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header bg-gray-50">
                <h3 class="font-semibold">Rendez-vous par type</h3>
            </div>
            <div class="card-body">
                <canvas id="typeChart" height="250"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50">
                <h3 class="font-semibold">Statut des rendez-vous</h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-gray-50">
            <h3 class="font-semibold">Détail des rendez-vous</h3>
        </div>
        <div class="card-body">
            @php
                $appointments = \App\Models\Appointment::with(['patient.user', 'doctor.user'])
                    ->whereYear('date_time', $month->year)
                    ->whereMonth('date_time', $month->month)
                    ->get();
            @endphp
            
            @if($appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            脂
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Médecin</th>
                                <th>Type</th>
                                <th>Statut</th>
                            </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->date_time->format('d/m/Y H:i') }}</td>
                                <td>{{ $appointment->patient->user->name }}</td>
                                <td>Dr. {{ $appointment->doctor->user->name }}</td>
                                <td>
                                    @if($appointment->type == 'general')
                                        <span class="badge bg-primary">Général</span>
                                    @elseif($appointment->type == 'emergency')
                                        <span class="badge bg-danger">Urgence</span>
                                    @elseif($appointment->type == 'follow_up')
                                        <span class="badge bg-info">Suivi</span>
                                    @else
                                        <span class="badge bg-warning">Spécialiste</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appointment->status == 'confirmed')
                                        <span class="badge bg-success">Confirmé</span>
                                    @elseif($appointment->status == 'cancelled')
                                        <span class="badge bg-danger">Annulé</span>
                                    @elseif($appointment->status == 'completed')
                                        <span class="badge bg-secondary">Terminé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Aucun rendez-vous ce mois</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('typeChart'), {
        type: 'pie',
        data: {
            labels: ['Général', 'Urgence', 'Suivi', 'Spécialiste'],
            datasets: [{
                data: [
                    {{ $stats['appointments_by_type']['general'] }},
                    {{ $stats['appointments_by_type']['emergency'] }},
                    {{ $stats['appointments_by_type']['follow_up'] }},
                    {{ $stats['appointments_by_type']['specialist'] }}
                ],
                backgroundColor: ['#3b82f6', '#ef4444', '#10b981', '#f59e0b']
            }]
        }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Confirmés', 'Annulés', 'Terminés', 'En attente'],
            datasets: [{
                data: [
                    {{ $stats['confirmed_appointments'] }},
                    {{ $stats['cancelled_appointments'] }},
                    {{ $stats['completed_appointments'] }},
                    {{ $appointments->where('status', 'pending')->count() }}
                ],
                backgroundColor: ['#10b981', '#ef4444', '#6b7280', '#f59e0b']
            }]
        }
    });
</script>
@endsection