public function establish()
{
    return view('doctor.establish-document');
}

public function storePrescription(Request $request)
{
    $prescription = Prescription::create([
        'patient_id' => $request->patient_id,
        'doctor_id' => auth()->user()->doctor->id,
        'medications' => json_encode($request->medications),
        'instructions' => $request->instructions,
        'prescription_date' => now(),
        'status' => 'active',
    ]);
    
    $pdf = Pdf::loadView('pdf.prescription', compact('prescription'));
    $filename = 'ordonnance_' . $prescription->id . '.pdf';
    $pdf->save(storage_path('app/public/' . $filename));
    
    return response()->json(['success' => true, 'pdf_url' => '/storage/' . $filename]);
}