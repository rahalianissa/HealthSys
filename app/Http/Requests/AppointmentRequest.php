<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date_time' => 'required|date|after:now',
            'duration' => 'nullable|integer|min:15|max:120',
            'type' => 'nullable|string|in:general,emergency,follow_up,specialist',
            'status' => 'nullable|string|in:pending,confirmed,cancelled,completed',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Le patient est requis.',
            'doctor_id.required' => 'Le médecin est requis.',
            'date_time.required' => 'La date et l\'heure sont requises.',
            'date_time.after' => 'La date doit être dans le futur.',
        ];
    }
}