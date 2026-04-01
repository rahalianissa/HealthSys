@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Rapports et exports</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h5>📊 Exports CSV</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('export.patients') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-file-csv"></i> Exporter patients (CSV)
                            </a>
                            <a href="{{ route('export.appointments') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-file-csv"></i> Exporter rendez-vous (CSV)
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white">
                            <h5>📈 Statistiques</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statsChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('statsChart'), {
        type: 'line',
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
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                fill: true
            }]
        }
    });
</script>
@endsection