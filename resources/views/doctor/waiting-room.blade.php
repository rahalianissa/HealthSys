@extends('layouts.app')

@section('title', 'Salle d\'attente')
@section('page-title', 'Salle d\'attente')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Patients en attente</h5>
            </div>
            <div class="card-body">
                @if($waiting->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Heure d'arrivée</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($waiting as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->patient->user->name }}</strong><br>
                                        <small>{{ $item->patient->user->phone }}</small>
                                    </td>
                                    <td>{{ $item->arrival_time->format('H:i') }} ({{ $item->arrival_time->diffForHumans() }})</td>
                                    <td>
                                        @if($item->priority == 2)
                                            <span class="badge bg-danger">Urgent</span>
                                        @elseif($item->priority == 1)
                                            <span class="badge bg-warning">Prioritaire</span>
                                        @else
                                            <span class="badge bg-secondary">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'waiting')
                                            <span class="badge bg-warning">En attente</span>
                                        @else
                                            <span class="badge bg-success">En consultation</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'waiting')
                                            <form action="{{ route('doctor.consultation.start', $item) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-custom btn-sm">
                                                    <i class="fas fa-stethoscope"></i> Valider visite
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Pas de patients dans la salle d'attente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection