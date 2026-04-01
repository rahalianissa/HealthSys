@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Patients</h1>
            <p class="text-gray-500 mt-1">Gestion des patients du cabinet</p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn-primary flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Nouveau patient</span>
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            @if($patients->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>Âge</th>
                            <th>Mutuelle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-primary-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $patient->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $patient->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $patient->user->phone }}</p>
                                <p class="text-xs text-gray-500">{{ $patient->user->address ?? 'Pas d\'adresse' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $patient->age }} ans</td>
                            <td class="px-6 py-4">
                                @if($patient->insurance_number)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">{{ $patient->insurance_company ?? 'Mutuelle' }}</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">Aucune</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-800" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="text-yellow-600 hover:text-yellow-800" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Supprimer ce patient ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 mb-4">Aucun patient enregistré</p>
                    <a href="{{ route('patients.create') }}" class="btn-primary inline-flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Ajouter votre premier patient</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection