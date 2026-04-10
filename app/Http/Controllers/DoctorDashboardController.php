<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;

class DoctorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function index()
    {
        $doctor = auth()->user()->doctor;
        
        $stats = [
            'today_appointments' => Appointment::where('doctor_id', $doctor->id)
                ->whereDate('date_time', today())->count(),
            'total_appointments' => Appointment::where('doctor_id', $doctor->id)->count(),
            'total_consultations' => Consultation::where('doctor_id', $doctor->id)->count(),
            'pending_appointments' => Appointment::where('doctor_id', $doctor->id)
                ->where('status', 'pending')->count(),
        ];
        
        $todayAppointments = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('date_time', today())
            ->orderBy('date_time')
            ->get();
            
        return view('doctor.dashboard', compact('stats', 'todayAppointments'));
    }
}