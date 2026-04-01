@extends('layouts.app')

@section('title', 'Rapport annuel')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rapport annuel</h1>
            <p class="text-gray-500 mt-1">Année {{ $year }}</p>
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

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total rendez-vous</p>
                        <p class="text-2xl font-bold">{{ $stats['total_appointments'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Nouveaux patients</p>
                        <p class="text-2xl font-bold">{{ $stats['total_patients'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_revenue'], 2) }} DT</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Montant payé</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_paid'], 2) }} DT</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique annuel -->
    <div class="card">
        <div class="card-header bg-gray-50">
            <h3 class="font-semibold text-gray-800">Évolution mensuelle</h3>
        </div>
        <div class="card-body">
            <canvas id="yearlyChart" height="300"></canvas>
        </div>
    </div>

    <!-- Tableau mensuel -->
    <div class="card">
        <div class="card-header bg-gray-50">
            <h3 class="font-semibold text-gray-800">Détail mensuel</h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        脂
                            <th>Mois</th>
                            <th>Rendez-vous</th>
                            <th>Chiffre d'affaires</th>
                            <th>Moyenne par RDV</th>
                        </thead>
                        <tbody>
                            @foreach($stats['monthly_data'] as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium">{{ $data['month'] }}</td>
                                <td class="px-6 py-4">{{ $data['appointments'] }}</td>
                                <td class="px-6 py-4">{{ number_format($data['revenue'], 2) }} DT</td>
                                <td class="px-6 py-4">
                                    @if($data['appointments'] > 0)
                                        {{ number_format($data['revenue'] / $data['appointments'], 2) }} DT
                                    @else
                                        0.00 DT
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td class="px-6 py-4">Total</td>
                                <td class="px-6 py-4">{{ $stats['total_appointments'] }}</td>
                                <td class="px-6 py-4">{{ number_format($stats['total_revenue'], 2) }} DT</td>
                                <td class="px-6 py-4">
                                    @if($stats['total_appointments'] > 0)
                                        {{ number_format($stats['total_revenue'] / $stats['total_appointments'], 2) }} DT
                                    @else
                                        0.00 DT
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('yearlyChart'), {
        type: 'line',
        data: {
            labels: [
                @foreach($stats['monthly_data'] as $data)
                    '{{ $data['month'] }}',
                @endforeach
            ],
            datasets: [
                {
                    label: 'Rendez-vous',
                    data: [
                        @foreach($stats['monthly_data'] as $data)
                            {{ $data['appointments'] }},
                        @endforeach
                    ],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Chiffre d\'affaires (DT)',
                    data: [
                        @foreach($stats['monthly_data'] as $data)
                            {{ $data['revenue'] }},
                        @endforeach
                    ],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            if (context.dataset.label.includes('DT')) {
                                return label + ': ' + value.toFixed(2) + ' DT';
                            }
                            return label + ': ' + value;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre de rendez-vous'
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Chiffre d\'affaires (DT)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
</script>
@endsection