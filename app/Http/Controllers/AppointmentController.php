<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Services\AppointmentService;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\OnlineBookingRequest;
use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->middleware('auth');
        $this->middleware('role:secretaire,chef_medecine')->except([
            'patientIndex', 'bookOnline', 'cancelOnline'
        ]);
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->date_from) {
            $query->whereDate('date_time', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('date_time', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('date_time', 'desc')->paginate(15);

        if ($request->wantsJson()) {
            return AppointmentResource::collection($appointments);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(AppointmentRequest $request)
    {
        $appointment = $this->appointmentService->create($request->validated());

        if ($appointment->patient && $appointment->patient->user) {
            $appointment->patient->user->notify(
                \App\Notifications\AppointmentNotification::appointmentConfirmation($appointment)
            );
        }

        $redirectUrl = auth()->user()->hasRole('secretaire') 
            ? '/secretaire/appointments' 
            : route('appointments.index');

        return redirect()->to($redirectUrl)->with('success', 'Rendez-vous créé avec succès');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        return view('appointments.show', compact('appointment'));
    }

    public function showJson(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);
        return response()->json($appointment);
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $appointment = $this->appointmentService->update($appointment, $request->validated());

        $redirectUrl = auth()->user()->hasRole('secretaire') 
            ? '/secretaire/appointments' 
            : route('appointments.index');

        return redirect()->to($redirectUrl)->with('success', 'Rendez-vous modifié avec succès');
    }

    public function destroy(Appointment $appointment)
    {
        $this->appointmentService->delete($appointment);
        
        $redirectUrl = auth()->user()->hasRole('secretaire') 
            ? '/secretaire/appointments' 
            : route('appointments.index');

        return redirect()->to($redirectUrl)->with('success', 'Rendez-vous supprimé avec succès');
    }

    public function patientIndex(Request $request)
    {
        $user = auth()->user();
        $patient = $this->getOrCreatePatient($user);

        $query = Appointment::with(['doctor.user'])
            ->where('patient_id', $patient->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date_time', 'desc')->paginate(10);

        if ($request->wantsJson()) {
            return AppointmentResource::collection($appointments);
        }

        $doctors = Doctor::with('user')->get();
        return view('patient.appointments', compact('appointments', 'doctors'));
    }

    public function bookOnline(OnlineBookingRequest $request)
    {
        $user = auth()->user();
        $patient = $this->getOrCreatePatient($user);

        if (!$this->appointmentService->isSlotAvailable(
            $request->doctor_id, 
            Carbon::parse($request->date)
        )) {
            return response()->json([
                'success' => false, 
                'message' => 'Ce créneau n\'est pas disponible'
            ], 409);
        }

        $appointment = $this->appointmentService->create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'date_time' => $request->date,
            'reason' => $request->reason,
            'status' => 'pending',
            'type' => 'general',
            'duration' => 30,
        ]);

        return response()->json([
            'success' => true, 
            'appointment' => new AppointmentResource($appointment)
        ], 201);
    }

    public function cancelOnline($id)
    {
        $user = auth()->user();
        $patient = $user->patient;

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Patient non trouvé'], 404);
        }

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $this->appointmentService->cancel($appointment);

        return response()->json(['success' => true]);
    }

    private function getOrCreatePatient($user)
    {
        if ($user->patient) {
            return $user->patient;
        }
        return Patient::create(['user_id' => $user->id]);
    }
}