<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\WaitingRoom;

class SecretaryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:secretaire');
    }

    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('date_time', today())->count(),
            'waiting_patients' => WaitingRoom::where('status', 'waiting')->count(),
            'monthly_revenue' => Invoice::whereMonth('created_at', now()->month)->sum('amount') ?? 0,
        ];
        
        $todayAppointments = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('date_time', today())
            ->orderBy('date_time')
            ->get();
            
        $recentPatients = Patient::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('secretaire.dashboard', compact('stats', 'todayAppointments', 'recentPatients'));
    }
}