<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Services\ConsultationService;
use App\Http\Requests\ConsultationRequest;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    protected ConsultationService $consultationService;

    public function __construct(ConsultationService $consultationService)
    {
        $this->middleware('auth');
        $this->consultationService = $consultationService;
    }

    public function index(Request $request)
    {
        $query = Consultation::with(['patient.user', 'doctor.user']);

        if ($request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->date_from) {
            $query->whereDate('consultation_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('consultation_date', '<=', $request->date_to);
        }

        $consultations = $query->orderBy('consultation_date', 'desc')->paginate(15);
        return view('consultations.index', compact('consultations'));
    }

    public function create(Request $request)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('status', 'confirmed')
            ->where('date_time', '<', now())
            ->get();
        
        $appointmentId = $request->appointment_id;
        $patientId = $request->patient_id;
        
        return view('consultations.create', compact('patients', 'doctors', 'appointments', 'appointmentId', 'patientId'));
    }

    public function store(ConsultationRequest $request)
    {
        $consultation = $this->consultationService->create($request->validated());
        return redirect()->route('consultations.show', $consultation)->with('success', 'Consultation enregistrée avec succès');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient.user', 'doctor.user', 'appointment']);
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('consultations.edit', compact('consultation', 'patients', 'doctors'));
    }

    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        $consultation = $this->consultationService->update($consultation, $request->validated());
        return redirect()->route('consultations.show', $consultation)->with('success', 'Consultation modifiée avec succès');
    }

    public function destroy(Consultation $consultation)
    {
        $this->consultationService->delete($consultation);
        return redirect()->route('consultations.index')->with('success', 'Consultation supprimée avec succès');
    }

    public function doctorConsultations(Request $request)
    {
        $doctor = auth()->user()->doctor;
        $query = Consultation::with(['patient.user'])->where('doctor_id', $doctor->id);
            
        if ($request->date_from) {
            $query->whereDate('consultation_date', '>=', $request->date_from);
        }
        
        $consultations = $query->orderBy('consultation_date', 'desc')->paginate(15);
        return view('doctor.consultations', compact('consultations'));
    }

    public function patientMedicalRecord(Request $request)
    {
        $patient = auth()->user()->patient;
        
        if (!$patient) {
            return redirect()->route('home')->with('error', 'Profil patient non trouvé');
        }
        
        $consultations = Consultation::with(['doctor.user'])
            ->where('patient_id', $patient->id)
            ->orderBy('consultation_date', 'desc')
            ->paginate(10);
        
        return view('patient.medical-record', compact('consultations'));
    }

    public function visitHistory()
    {
        $consultations = Consultation::with(['patient.user'])
            ->where('doctor_id', auth()->user()->doctor->id)
            ->orderBy('consultation_date', 'desc')
            ->get();
        
        return view('doctor.history', compact('consultations'));
    }

    public function details(Consultation $consultation)
    {
        $consultation->load(['patient.user', 'doctor.user']);
        return response()->json($consultation);
    }
}