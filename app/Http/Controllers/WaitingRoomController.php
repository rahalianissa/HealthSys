<?php

namespace App\Http\Controllers;

use App\Models\WaitingRoom;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class WaitingRoomController extends Controller
{
    public function index()
    {
        $waiting = WaitingRoom::with(['patient.user', 'doctor.user'])
            ->where('status', 'waiting')
            ->orderBy('priority', 'desc')
            ->orderBy('arrival_time', 'asc')
            ->get();

        $inConsultation = WaitingRoom::with(['patient.user', 'doctor.user'])
            ->where('status', 'in_consultation')
            ->first();

        $doctors = Doctor::with('user')->get();

        return view('waiting-room.index', compact('waiting', 'inConsultation', 'doctors'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'priority' => 'nullable|integer',
        ]);

        $existing = WaitingRoom::where('patient_id', $request->patient_id)
            ->where('status', 'waiting')
            ->first();

        if ($existing) {
            return back()->with('error', 'Patient déjà en salle d\'attente');
        }

        WaitingRoom::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'arrival_time' => now(),
            'priority' => $request->priority ?? 0,
            'status' => 'waiting',
        ]);

        return redirect()->route('waiting-room')->with('success', 'Patient ajouté à la salle d\'attente');
    }

    public function startConsultation(WaitingRoom $waitingRoom)
    {
        $waitingRoom->update(['status' => 'in_consultation']);
        return redirect()->route('waiting-room')->with('success', 'Consultation démarrée');
    }

    public function complete(WaitingRoom $waitingRoom)
    {
        $waitingRoom->update(['status' => 'completed']);
        return redirect()->route('waiting-room')->with('success', 'Consultation terminée');
    }

    public function remove(WaitingRoom $waitingRoom)
    {
        $waitingRoom->delete();
        return redirect()->route('waiting-room')->with('success', 'Patient retiré de la salle d\'attente');
    }
}