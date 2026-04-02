public function patientIndex()
{
    $appointments = Appointment::with(['doctor.user'])
        ->where('patient_id', auth()->user()->patient->id)
        ->orderBy('date_time', 'desc')
        ->get();
    
    if (request()->wantsJson()) {
        return response()->json($appointments);
    }
    
    $doctors = Doctor::with('user')->get();
    return view('patient.appointments', compact('appointments', 'doctors'));
}

public function bookOnline(Request $request)
{
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'date' => 'required|date|after:now',
    ]);
    
    $dateTime = Carbon::parse($request->date);
    
    // Vérifier disponibilité
    $exists = Appointment::where('doctor_id', $request->doctor_id)
        ->whereDate('date_time', $dateTime)
        ->where('status', '!=', 'cancelled')
        ->exists();
    
    if ($exists) {
        return response()->json(['success' => false, 'message' => 'Ce créneau n\'est pas disponible']);
    }
    
    $appointment = Appointment::create([
        'patient_id' => auth()->user()->patient->id,
        'doctor_id' => $request->doctor_id,
        'date_time' => $dateTime,
        'reason' => $request->reason,
        'status' => 'pending',
        'type' => 'general',
        'duration' => 30,
    ]);
    
    return response()->json(['success' => true, 'appointment' => $appointment]);
}

public function cancelOnline($id)
{
    $appointment = Appointment::where('id', $id)
        ->where('patient_id', auth()->user()->patient->id)
        ->firstOrFail();
    
    $appointment->update(['status' => 'cancelled']);
    
    return response()->json(['success' => true]);
}