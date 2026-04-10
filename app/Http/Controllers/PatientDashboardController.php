<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Prescription;

class PatientDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function index()
    {
        $patient = auth()->user()->patient;
        
        if (!$patient) {
            $patient = \App\Models\Patient::create(['user_id' => auth()->id()]);
            auth()->user()->refresh();
        }
        
        $stats = [
            'upcoming_appointments' => Appointment::where('patient_id', $patient->id)
                ->where('date_time', '>', now())
                ->where('status', '!=', 'cancelled')->count(),
            'total_consultations' => $patient->consultations()->count(),
            'pending_invoices' => Invoice::where('patient_id', $patient->id)
                ->where('status', 'pending')->count(),
            'active_prescriptions' => Prescription::where('patient_id', $patient->id)
                ->where('status', 'active')->count(),
        ];
        
        $nextAppointment = Appointment::with(['doctor.user'])
            ->where('patient_id', $patient->id)
            ->where('date_time', '>', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('date_time', 'asc')
            ->first();
            
        return view('patient.dashboard', compact('stats', 'nextAppointment'));
    }
}