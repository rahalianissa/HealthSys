<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class DoctorController extends Controller
{

    public function index()
    {
        $doctors = Doctor::with('user')->get();
        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
            'specialty' => 'required',
            'registration_number' => 'required|unique:doctors',
            'consultation_fee' => 'required|numeric',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialty' => $request->specialty,
            'registration_number' => $request->registration_number,
            'consultation_fee' => $request->consultation_fee,
            'diploma' => $request->diploma,
            'cabinet_phone' => $request->cabinet_phone,
            'schedule' => $request->schedule,
        ]);

        return redirect()->route('doctors.index')
            ->with('success', 'Médecin ajouté avec succès');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'appointments.patient.user']);
        
        $stats = [
            'total_appointments' => $doctor->appointments->count(),
            'completed_appointments' => $doctor->appointments->where('status', 'completed')->count(),
            'cancelled_appointments' => $doctor->appointments->where('status', 'cancelled')->count(),
            'upcoming_appointments' => $doctor->appointments->where('date_time', '>', now())->where('status', 'confirmed')->count(),
            'total_revenue' => $doctor->appointments->sum('consultation_fee'),
        ];
        
        return view('doctors.show', compact('doctor', 'stats'));
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'phone' => 'required',
            'specialty' => 'required',
            'registration_number' => 'required|unique:doctors,registration_number,' . $doctor->id,
            'consultation_fee' => 'required|numeric',
        ]);

        $doctor->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
        ]);

        $doctor->update([
            'specialty' => $request->specialty,
            'registration_number' => $request->registration_number,
            'consultation_fee' => $request->consultation_fee,
            'diploma' => $request->diploma,
            'cabinet_phone' => $request->cabinet_phone,
            'schedule' => $request->schedule,
        ]);

        return redirect()->route('doctors.index')
            ->with('success', 'Médecin modifié avec succès');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->user->delete();
        $doctor->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Médecin supprimé avec succès');
    }

    public function schedule(Doctor $doctor)
    {
        $appointments = $doctor->appointments()
            ->with('patient.user')
            ->where('date_time', '>=', now())
            ->orderBy('date_time')
            ->get();
            
        return view('doctors.schedule', compact('doctor', 'appointments'));
    }

    public function exportPdf()
    {
        $doctors = Doctor::with('user')->get();
        $pdf = Pdf::loadView('pdf.doctors', compact('doctors'));
        return $pdf->download('medecins_' . date('Y-m-d') . '.pdf');
    }
}