<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->orderBy('date_time', 'desc')
            ->get();
        
        return view('appointments.index', compact('appointments'));
    }

    public function calendar()
    {
        return view('appointments.calendar');
    }

    public function getEvents()
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user'])->get();
        
        $events = [];
        foreach ($appointments as $appointment) {
            $color = $this->getStatusColor($appointment->status);
            
            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->patient->user->name . ' - Dr. ' . $appointment->doctor->user->name,
                'start' => $appointment->date_time->toISOString(),
                'end' => $appointment->date_time->copy()->addMinutes($appointment->duration)->toISOString(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $appointment->status,
                    'type' => $appointment->type,
                    'reason' => $appointment->reason,
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                ]
            ];
        }
        
        return response()->json($events);
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'cancelled' => '#dc3545',
            'completed' => '#6c757d',
            default => '#007bff',
        };
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        
        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:15|max:120',
            'type' => 'required',
            'reason' => 'nullable',
        ]);

        $dateTime = Carbon::parse($request->date . ' ' . $request->time);

        // Vérifier si le créneau est disponible
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('date_time', $dateTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce créneau est déjà pris')->withInput();
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date_time' => $dateTime,
            'duration' => $request->duration,
            'status' => 'pending',
            'type' => $request->type,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous créé avec succès');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:15|max:120',
            'status' => 'required',
            'type' => 'required',
        ]);

        $dateTime = Carbon::parse($request->date . ' ' . $request->time);

        // Vérifier si le créneau est disponible (sauf pour ce rendez-vous)
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('date_time', $dateTime)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce créneau est déjà pris')->withInput();
        }

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date_time' => $dateTime,
            'duration' => $request->duration,
            'status' => $request->status,
            'type' => $request->type,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous modifié avec succès');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous supprimé avec succès');
    }

    public function changeStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $appointment->update(['status' => $request->status]);

        return back()->with('success', 'Statut du rendez-vous mis à jour');
    }
}