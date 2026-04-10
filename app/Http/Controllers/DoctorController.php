<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialite;
use App\Models\Appointment;
use App\Services\UserService;
use App\Http\Requests\DoctorRequest;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->middleware('role:chef_medecine')->except(['index', 'myPatients', 'notifications', 'markAllNotifications', 'markNotificationRead', 'showPatient']);
        $this->userService = $userService;
    }

    public function index()
    {
        $doctors = Doctor::with('user')->paginate(15);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialites = Specialite::all();
        return view('admin.doctors.create', compact('specialites'));
    }

    public function store(DoctorRequest $request)
    {
        $this->userService->createDoctor($request->validated());
        return redirect()->route('admin.doctors.index')->with('success', 'Médecin ajouté avec succès');
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        $specialites = Specialite::all();
        return view('admin.doctors.edit', compact('doctor', 'specialites'));
    }

    public function update(DoctorRequest $request, Doctor $doctor)
    {
        $this->userService->updateDoctor($doctor, $request->validated());
        return redirect()->route('admin.doctors.index')->with('success', 'Médecin modifié avec succès');
    }

    public function destroy(Doctor $doctor)
    {
        $this->userService->deleteDoctor($doctor);
        return redirect()->route('admin.doctors.index')->with('success', 'Médecin supprimé avec succès');
    }

    public function myPatients(Request $request)
    {
        $doctorId = auth()->user()->doctor->id;
        
        $query = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->select('patient_id')
            ->distinct();
            
        if ($request->search) {
            $query->whereHas('patient.user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }
        
        $patients = $query->get()->pluck('patient');
        return view('doctor.patients', compact('patients'));
    }

    public function showPatient(Patient $patient)
    {
        $patient->load(['user', 'consultations.doctor.user', 'prescriptions.doctor.user']);
        return view('doctor.patient-show', compact('patient'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('doctor.notifications', compact('notifications'));
    }

    public function markAllNotifications()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->back()->with('success', 'Notification marquée comme lue');
    }
}