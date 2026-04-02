<?php

namespace App\Http\Controllers;

use App\Models\WaitingRoom;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
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
        $patients = Patient::with('user')->get();

        $stats = [
            'total_waiting' => $waiting->count(),
            'average_wait_time' => $this->calculateAverageWaitTime(),
            'completed_today' => WaitingRoom::whereDate('created_at', today())->where('status', 'completed')->count(),
        ];

        return view('waiting-room.index', compact('waiting', 'inConsultation', 'doctors', 'patients', 'stats'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'priority' => 'nullable|integer|min:0|max:2',
        ]);

        $existing = WaitingRoom::where('patient_id', $request->patient_id)
            ->whereIn('status', ['waiting', 'in_consultation'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Patient déjà en salle d\'attente');
        }

        WaitingRoom::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_id' => $request->appointment_id,
            'arrival_time' => now(),
            'priority' => $request->priority ?? 0,
            'status' => 'waiting',
        ]);

        return redirect()->route('waiting-room')->with('success', 'Patient ajouté à la salle d\'attente');
    }

    public function startConsultation(WaitingRoom $waitingRoom)
    {
        $waitingRoom->update([
            'status' => 'in_consultation',
            'start_time' => now(),
        ]);

        return redirect()->route('waiting-room')->with('success', 'Consultation démarrée');
    }

    public function complete(WaitingRoom $waitingRoom)
    {
        $waitingRoom->update([
            'status' => 'completed',
            'end_time' => now(),
        ]);

        return redirect()->route('waiting-room')->with('success', 'Consultation terminée');
    }

    public function remove(WaitingRoom $waitingRoom)
    {
        $waitingRoom->delete();
        return redirect()->route('waiting-room')->with('success', 'Patient retiré de la salle d\'attente');
    }

    public function updatePriority(Request $request, WaitingRoom $waitingRoom)
    {
        $request->validate([
            'priority' => 'required|integer|min:0|max:2',
        ]);

        $waitingRoom->update(['priority' => $request->priority]);

        return redirect()->route('waiting-room')->with('success', 'Priorité mise à jour');
    }

    private function calculateAverageWaitTime()
    {
        $completed = WaitingRoom::where('status', 'completed')
            ->whereNotNull('start_time')
            ->get();
            
        if ($completed->count() == 0) {
            return 0;
        }
        
        $totalWait = 0;
        foreach ($completed as $item) {
            $totalWait += $item->arrival_time->diffInMinutes($item->start_time);
        }
        
        return round($totalWait / $completed->count());
    }

    public function getQueue()
    {
        $waiting = WaitingRoom::with(['patient.user', 'doctor.user'])
            ->where('status', 'waiting')
            ->orderBy('priority', 'desc')
            ->orderBy('arrival_time', 'asc')
            ->get();
            
        return response()->json($waiting);
    }
}