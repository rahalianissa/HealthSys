<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSys - Cabinet Médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-hospital-user"></i> HealthSys
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1 class="display-4">HealthSys</h1>
                <p class="lead">Système de gestion de cabinet médical</p>
                <p>Gérez vos patients, médecins et rendez-vous facilement.</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Commencer</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Accéder au dashboard</a>
                @endguest
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-4x text-primary mb-3"></i>
                        <h5>Statistiques</h5>
                        <p>{{ \App\Models\Patient::count() }} Patients</p>
                        <p>{{ \App\Models\Doctor::count() }} Médecins</p>
                        <p>{{ \App\Models\Appointment::count() }} Rendez-vous</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>