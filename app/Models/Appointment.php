<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'date_time', 'duration',
        'status', 'type', 'reason', 'notes', 'reminder_sent'
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->date_time->format('d/m/Y H:i');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'secondary',
        ];

        $text = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'cancelled' => 'Annulé',
            'completed' => 'Terminé',
        ];

        return '<span class="badge bg-' . $badges[$this->status] . '">' . $text[$this->status] . '</span>';
    }
}