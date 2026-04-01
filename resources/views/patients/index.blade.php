@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Liste des patients</h3>
            <a href="{{ route('patients.create') }}" class="btn btn-primary">+ Ajouter</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if($patients->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td>{{ $patient->user->name }}</td>
                            <td>{{ $patient->user->email }}</td>
                            <td>{{ $patient->user->phone }}</td>
                            <td>
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('patients.destroy', $patient) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Aucun patient</p>
            @endif
        </div>
    </div>
</div>
@endsection