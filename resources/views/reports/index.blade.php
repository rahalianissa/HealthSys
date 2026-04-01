@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rapports et analyses</h1>
            <p class="text-gray-500 mt-1">Analyse des données du cabinet</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rapport Mensuel -->
        <div class="card">
            <div class="card-header bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-calendar-alt text-primary-600 mr-2"></i>
                    Rapport mensuel
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.monthly') }}" method="GET">
                    <div class="mb-4">
                        <label class="form-label">Mois</label>
                        <input type="month" name="month" class="form-input" value="{{ date('Y-m') }}">
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-chart-line"></i> Générer le rapport
                    </button>
                </form>
            </div>
        </div>

        <!-- Rapport Annuel -->
        <div class="card">
            <div class="card-header bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-chart-line text-primary-600 mr-2"></i>
                    Rapport annuel
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.yearly') }}" method="GET">
                    <div class="mb-4">
                        <label class="form-label">Année</label>
                        <select name="year" class="form-input">
                            @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-chart-line"></i> Générer le rapport
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total patients</p>
                        <p class="text-2xl font-bold">{{ \App\Models\Patient::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total médecins</p>
                        <p class="text-2xl font-bold">{{ \App\Models\Doctor::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-md text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total rendez-vous</p>
                        <p class="text-2xl font-bold">{{ \App\Models\Appointment::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
                        <p class="text-2xl font-bold">{{ number_format(\App\Models\Invoice::sum('amount'), 2) }} DT</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique rapide -->
    <div class="card">
        <div class="card-header bg-gray-50">
            <h3 class="font-semibold text-gray-800">Évolution des rendez-vous</h3>
        </div>
        <div class="card-body">
            <canvas id="yearlyChart" height="300"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('yearlyChart'), {
        type: 'line',
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
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection