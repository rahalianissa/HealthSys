<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ];

        if ($user->role == 'doctor' && $user->doctor) {
            $rules['specialty'] = 'nullable|string';
            $rules['registration_number'] = 'nullable|string';
            $rules['consultation_fee'] = 'nullable|numeric';
        }

        $request->validate($rules);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        if ($user->role == 'doctor' && $user->doctor) {
            $doctor = $user->doctor;
            $doctor->specialty = $request->specialty ?? $doctor->specialty;
            $doctor->registration_number = $request->registration_number ?? $doctor->registration_number;
            $doctor->consultation_fee = $request->consultation_fee ?? $doctor->consultation_fee;
            $doctor->save();
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour avec succès');
    }
}