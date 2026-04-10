<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor') ? $this->route('doctor')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->user_id ?? 'NULL'),
            'password' => $this->isMethod('POST') ? 'required|min:6' : 'nullable|min:6',
            'phone' => 'required|string',
            'specialty' => 'required|string',
            'registration_number' => 'required|string|unique:doctors,registration_number,' . $doctorId,
            'consultation_fee' => 'required|numeric|min:0',
            'specialite_id' => 'nullable|exists:specialites,id',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'diploma' => 'nullable|string',
            'cabinet_phone' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est requis.',
            'phone.required' => 'Le téléphone est requis.',
            'specialty.required' => 'La spécialité est requise.',
            'registration_number.required' => 'Le numéro d\'inscription est requis.',
            'registration_number.unique' => 'Ce numéro d\'inscription existe déjà.',
            'consultation_fee.required' => 'Le tarif de consultation est requis.',
        ];
    }
}