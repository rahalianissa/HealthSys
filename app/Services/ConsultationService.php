<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class ConsultationService
{
    public function create(array $data): Consultation
    {
        return DB::transaction(function () use ($data) {
            $consultation = Consultation::create($data);
            
            if (isset($data['appointment_id'])) {
                Appointment::where('id', $data['appointment_id'])->update(['status' => 'completed']);
            }
            
            return $consultation;
        });
    }

    public function update(Consultation $consultation, array $data): Consultation
    {
        return DB::transaction(function () use ($consultation, $data) {
            $consultation->update($data);
            return $consultation;
        });
    }

    public function delete(Consultation $consultation): bool
    {
        return DB::transaction(function () use ($consultation) {
            return $consultation->delete();
        });
    }
}