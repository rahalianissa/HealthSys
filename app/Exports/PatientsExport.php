<?php

namespace App\Exports;

use App\Models\Patient;

class PatientsExport
{
    public function export()
    {
        $patients = Patient::with('user')->get();
        
        $filename = storage_path('app/public/patients_' . date('Y-m-d') . '.csv');
        $handle = fopen($filename, 'w');
        
        // Entêtes
        fputcsv($handle, ['ID', 'Nom', 'Email', 'Téléphone', 'Date naissance', 'Adresse', 'Mutuelle', 'Allergies']);
        
        // Données
        foreach ($patients as $patient) {
            fputcsv($handle, [
                $patient->id,
                $patient->user->name,
                $patient->user->email,
                $patient->user->phone,
                $patient->user->birth_date,
                $patient->user->address,
                $patient->insurance_company . ' ' . $patient->insurance_number,
                $patient->allergies,
            ]);
        }
        
        fclose($handle);
        
        return $filename;
    }
}