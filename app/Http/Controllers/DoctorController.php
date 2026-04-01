<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        ]);

        return redirect()->route('doctors.index')
            ->with('success', 'Médecin ajouté avec succès');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load('user', 'appointments');
        return view('doctors.show', compact('doctor'));
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
}