<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Le patient est requis.',
            'amount.required' => 'Le montant est requis.',
            'issue_date.required' => 'La date d\'émission est requise.',
            'due_date.required' => 'La date d\'échéance est requise.',
            'due_date.after_or_equal' => 'La date d\'échéance doit être postérieure à la date d\'émission.',
        ];
    }
}