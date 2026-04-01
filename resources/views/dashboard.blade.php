@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Bonjour, {{ auth()->user()->name }} !</h1>
                    <p class="text-gray-500 mt-1">Voici le résumé de votre activité</p>
                </div>
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-primary-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card hover:shadow-lg transition-shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Patients</p>
                        <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Patient::count() }}</p>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-arrow-up"></i> +12% ce mois
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hover:shadow-lg transition-shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Médecins</p>
                        <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Doctor::count() }}</p>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-arrow-up"></i> +2 nouveaux
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-md text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hover:shadow-lg transition-shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Rendez-vous aujourd'hui</p>
                        <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Appointment::whereDate('date_time', today())->count() }}</p>
                        <p class="text-blue-600 text-sm mt-2">
                            <i class="fas fa-calendar"></i> {{ now()->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hover:shadow-lg transition-shadow">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Rendez-vous ce mois</p>
                        <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Appointment::whereMonth('date_time', now()->month)->count() }}</p>
                        <p class="text-purple-600 text-sm mt-2">
                            <i class="fas fa-chart-line"></i> {{ now()->format('F Y') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart Card -->
        <div class="card">
            <div class="card-header bg-gray-50">
                <h3 class="font-semibold text-gray-800">Évolution des rendez-vous</h3>
            </div>
            <div class="card-body">
                <canvas id="appointmentsChart" height="250"></canvas>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card">
            <div class="card-header bg-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Prochains rendez-vous</h3>
                <a href="{{ route('appointments.index') }}" class="text-primary-600 text-sm hover:underline">Voir tout</a>
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
                    <div class="space-y-3">
                        @foreach($upcoming as $appointment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-primary-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $appointment->patient->user->name }}</p>
                                        <p class="text-sm text-gray-500">Dr. {{ $appointment->doctor->user->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-800">{{ $appointment->date_time->format('H:i') }}</p>
                                    <p class="text-sm text-gray-500">{{ $appointment->date_time->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-check text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Aucun rendez-vous à venir</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Waiting Room -->
    <div class="card">
        <div class="card-header bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Salle d'attente</h3>
            <a href="{{ route('waiting-room') }}" class="text-primary-600 text-sm hover:underline">Gérer</a>
        </div>
        <div class="card-body">
            @php
                $waiting = \App\Models\WaitingRoom::with(['patient.user'])
                    ->where('status', 'waiting')
                    ->orderBy('priority', 'desc')
                    ->orderBy('arrival_time', 'asc')
                    ->limit(5)
                    ->get();
            @endphp
            
            @if($waiting->count() > 0)
                <div class="space-y-2">
                    @foreach($waiting as $item)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @if($item->priority == 2)
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                @elseif($item->priority == 1)
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                @else
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                @endif
                                <span class="font-medium">{{ $item->patient->user->name }}</span>
                                <span class="text-sm text-gray-500">Arrivé à {{ $item->arrival_time->format('H:i') }}</span>
                            </div>
                            @if($item->priority == 2)
                                <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full">Urgent</span>
                            @elseif($item->priority == 1)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-600 text-xs rounded-full">Prioritaire</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">Aucun patient en attente</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    new Chart(document.getElementById('appointmentsChart'), {
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
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection