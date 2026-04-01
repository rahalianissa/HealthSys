<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HealthSys - @yield('title', 'Gestion Cabinet Médical')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar-item {
            @apply flex items-center space-x-3 px-4 py-3 rounded-lg transition duration-200;
        }
        .sidebar-item:hover {
            @apply bg-primary-50 text-primary-600;
        }
        .sidebar-item.active {
            @apply bg-primary-600 text-white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-white shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hospital-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">HealthSys</h1>
                        <p class="text-xs text-gray-500">Cabinet médical</p>
                    </div>
                </div>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Tableau de bord</span>
                </a>
                
                <a href="{{ route('patients.index') }}" class="sidebar-item {{ request()->routeIs('patients.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-users w-5"></i>
                    <span>Patients</span>
                </a>
                
                <a href="{{ route('doctors.index') }}" class="sidebar-item {{ request()->routeIs('doctors.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-user-md w-5"></i>
                    <span>Médecins</span>
                </a>
                
                <a href="{{ route('appointments.index') }}" class="sidebar-item {{ request()->routeIs('appointments.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-calendar-alt w-5"></i>
                    <span>Rendez-vous</span>
                </a>
                
                <a href="{{ route('calendar') }}" class="sidebar-item {{ request()->routeIs('calendar') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-calendar-week w-5"></i>
                    <span>Calendrier</span>
                </a>
                
                <a href="{{ route('waiting-room') }}" class="sidebar-item {{ request()->routeIs('waiting-room') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-clock w-5"></i>
                    <span>Salle d'attente</span>
                </a>
                
                <a href="{{ route('prescriptions.index') }}" class="sidebar-item {{ request()->routeIs('prescriptions.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-prescription w-5"></i>
                    <span>Ordonnances</span>
                </a>
                
                <a href="{{ route('invoices.index') }}" class="sidebar-item {{ request()->routeIs('invoices.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-file-invoice-dollar w-5"></i>
                    <span>Factures</span>
                </a>
                
                <a href="{{ route('reports.index') }}" class="sidebar-item {{ request()->routeIs('reports.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>Rapports</span>
                </a>
                
                @if(auth()->user()->role == 'admin')
                <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fas fa-users-cog w-5"></i>
                    <span>Utilisateurs</span>
                </a>
                @endif
            </nav>
            
            <div class="absolute bottom-0 w-72 p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <p class="text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>