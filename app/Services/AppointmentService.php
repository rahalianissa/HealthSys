<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function create(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $appointment = Appointment::create($data);
            return $appointment;
        });
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        return DB::transaction(function () use ($appointment, $data) {
            $oldStatus = $appointment->status;
            $appointment->update($data);
            
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $this->sendStatusNotification($appointment, $data['status']);
            }
            
            return $appointment;
        });
    }

    public function delete(Appointment $appointment): bool
    {
        return DB::transaction(function () use ($appointment) {
            return $appointment->delete();
        });
    }

    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'cancelled']);
        
        if ($appointment->patient && $appointment->patient->user) {
            $appointment->patient->user->notify(
                \App\Notifications\AppointmentNotification::appointmentCancellation($appointment)
            );
        }
        
        return $appointment;
    }

    public function isSlotAvailable(int $doctorId, Carbon $dateTime): bool
    {
        return !Appointment::where('doctor_id', $doctorId)
            ->where('status', '!=', 'cancelled')
            ->whereDate('date_time', $dateTime->toDateString())
            ->whereTime('date_time', $dateTime->toTimeString())
            ->exists();
    }

    private function sendStatusNotification(Appointment $appointment, string $newStatus): void
    {
        if (!$appointment->patient || !$appointment->patient->user) {
            return;
        }

        $notification = match($newStatus) {
            'confirmed' => \App\Notifications\AppointmentNotification::appointmentConfirmation($appointment),
            'cancelled' => \App\Notifications\AppointmentNotification::appointmentCancellation($appointment),
            default => null,
        };

        if ($notification) {
            $appointment->patient->user->notify($notification);
        }
    }
}