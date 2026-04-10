<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnlineBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after:tomorrow',
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required' => 'Veuillez sélectionner un médecin.',
            'date.required' => 'Veuillez sélectionner une date.',
            'date.after' => 'La réservation doit être faite au moins 24h à l\'avance.',
        ];
    }
}