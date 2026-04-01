<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;

class ExportPatients extends Command
{
    protected $signature = 'export:patients';
    protected $description = 'Export patients to CSV';

    public function handle()
    {
        $patients = Patient::with('user')->get();
        
        $filename = storage_path('app/patients_' . date('Y-m-d') . '.csv');
        $file = fopen($filename, 'w');
        
        fputcsv($file, ['ID', 'Nom', 'Email', 'Téléphone', 'Date naissance', 'Adresse', 'Mutuelle', 'Allergies']);
        
        foreach ($patients as $patient) {
            fputcsv($file, [
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
        
        fclose($file);
        
        $this->info('Export terminé: ' . $filename);
    }
}