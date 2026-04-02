<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Notifications\AppointmentReminder;
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

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date_time' => $dateTime,
            'duration' => $request->duration,
            'status' => 'pending',
            'type' => $request->type,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        // Envoyer notification de confirmation (CORRECTION ICI)
        $appointment->patient->user->notify(new AppointmentReminder($appointment, 'confirmation'));

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

        $oldStatus = $appointment->status;
        
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

        // Envoyer notification si le statut change
        if ($oldStatus != $request->status && $request->status == 'confirmed') {
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'confirmation'));
        } elseif ($oldStatus != $request->status && $request->status == 'cancelled') {
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'cancellation'));
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous modifié avec succès');
    }

    public function destroy(Appointment $appointment)
    {
        // Envoyer notification d'annulation avant suppression
        if ($appointment->status != 'cancelled' && $appointment->status != 'completed') {
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'cancellation'));
        }
        
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous supprimé avec succès');
    }

    public function changeStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldStatus = $appointment->status;
        
        $appointment->update(['status' => $request->status]);

        // Envoyer notification selon le nouveau statut
        if ($oldStatus != $request->status && $request->status == 'confirmed') {
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'confirmation'));
        } elseif ($oldStatus != $request->status && $request->status == 'cancelled') {
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'cancellation'));
        }

        return back()->with('success', 'Statut du rendez-vous mis à jour');
    }
    
    public function confirm(Appointment $appointment)
    {
        if ($appointment->status == 'pending') {
            $appointment->update(['status' => 'confirmed']);
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'confirmation'));
            return redirect()->route('appointments.index')
                ->with('success', 'Rendez-vous confirmé avec succès');
        }
        
        return redirect()->route('appointments.index')
            ->with('error', 'Impossible de confirmer ce rendez-vous');
    }
    
    public function cancel(Appointment $appointment)
    {
        if ($appointment->status != 'cancelled' && $appointment->status != 'completed') {
            $appointment->update(['status' => 'cancelled']);
            $appointment->patient->user->notify(new AppointmentReminder($appointment, 'cancellation'));
            return redirect()->route('appointments.index')
                ->with('success', 'Rendez-vous annulé avec succès');
        }
        
        return redirect()->route('appointments.index')
            ->with('error', 'Impossible d\'annuler ce rendez-vous');
    }
    
    public function complete(Appointment $appointment)
    {
        if ($appointment->status == 'confirmed') {
            $appointment->update(['status' => 'completed']);
            return redirect()->route('appointments.index')
                ->with('success', 'Rendez-vous marqué comme terminé');
        }
        
        return redirect()->route('appointments.index')
            ->with('error', 'Impossible de terminer ce rendez-vous');
    }
}