<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingRoom extends Model
{
    protected $fillable = [
        'appointment_id', 'patient_id', 'doctor_id', 
        'arrival_time', 'priority', 'status', 'notes'
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            2 => 'Urgent',
            1 => 'Prioritaire',
            default => 'Normal'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            2 => 'danger',
            1 => 'warning',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'waiting' => 'En attente',
            'in_consultation' => 'En consultation',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'waiting' => 'warning',
            'in_consultation' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
}