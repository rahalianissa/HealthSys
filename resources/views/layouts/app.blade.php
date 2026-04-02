<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HealthSys - @yield('title', 'Gestion Cabinet Médical')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-hospital-user"></i> HealthSys
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(auth()->user()->role == 'chef_medecine')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                        @elseif(auth()->user()->role == 'doctor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('doctor.waiting-room') }}">Salle d'attente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('doctor.consultations') }}">Consultations</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('doctor.history') }}">Historique</a>
                            </li>
                        @elseif(auth()->user()->role == 'secretaire')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('secretaire.comptabilite') }}">Comptabilité</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('secretaire.appointments.index') }}">Rendez-vous</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('secretaire.patients.index') }}">Patients</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('secretaire.documents') }}">Documents</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('secretaire.waiting-room') }}">Salle d'attente</a>
                            </li>
                        @elseif(auth()->user()->role == 'patient')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('patient.appointments') }}">Mes rendez-vous</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('patient.medical-record') }}">Dossier médical</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('patient.prescriptions') }}">Ordonnances</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('patient.invoices') }}">Factures</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Déconnexion</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>