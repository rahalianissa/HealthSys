<?php

namespace App\Services;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function createDoctor(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'doctor',
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'specialite_id' => $data['specialite_id'] ?? null,
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialty' => $data['specialty'],
            'registration_number' => $data['registration_number'],
            'consultation_fee' => $data['consultation_fee'],
            'diploma' => $data['diploma'] ?? null,
            'cabinet_phone' => $data['cabinet_phone'] ?? null,
        ]);

        return $user;
    }

    public function updateDoctor(Doctor $doctor, array $data): Doctor
    {
        $doctor->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'specialite_id' => $data['specialite_id'] ?? null,
        ]);

        if (!empty($data['password'])) {
            $doctor->user->update(['password' => Hash::make($data['password'])]);
        }

        $doctor->update([
            'specialty' => $data['specialty'],
            'registration_number' => $data['registration_number'],
            'consultation_fee' => $data['consultation_fee'],
            'diploma' => $data['diploma'] ?? null,
            'cabinet_phone' => $data['cabinet_phone'] ?? null,
        ]);

        return $doctor;
    }

    public function deleteDoctor(Doctor $doctor): void
    {
        $user = $doctor->user;
        $doctor->delete();
        $user->delete();
    }

    public function resetPassword(User $user): string
    {
        $newPassword = Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);
        return $newPassword;
    }
}