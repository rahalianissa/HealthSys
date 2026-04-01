<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function patients()
    {
        $patients = Patient::with('user')->get();
        
        $filename = 'patients_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($patients) {
            $file = fopen('php://output', 'w');
            
            // Entêtes CSV
            fputcsv($file, ['ID', 'Nom complet', 'Email', 'Téléphone', 'Date naissance', 'Adresse', 'Mutuelle', 'Allergies', 'Antécédents']);
            
            // Données
            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->id,
                    $patient->user->name,
                    $patient->user->email,
                    $patient->user->phone,
                    $patient->user->birth_date ?? '',
                    $patient->user->address ?? '',
                    $patient->insurance_company . ' ' . $patient->insurance_number,
                    $patient->allergies ?? '',
                    $patient->medical_history ?? '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function appointments()
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user'])->get();
        
        $filename = 'rendez-vous_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // Entêtes CSV
            fputcsv($file, ['ID', 'Patient', 'Médecin', 'Date', 'Heure', 'Type', 'Statut', 'Raison']);
            
            // Données
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->id,
                    $appointment->patient->user->name,
                    'Dr. ' . $appointment->doctor->user->name,
                    $appointment->date_time->format('d/m/Y'),
                    $appointment->date_time->format('H:i'),
                    $appointment->type,
                    $appointment->status,
                    $appointment->reason ?? '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}