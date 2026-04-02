<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'patient_id', 'consultation_id', 'amount',
        'paid_amount', 'status', 'issue_date', 'due_date', 'description'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' DT';
    }

    public function getFormattedPaidAttribute()
    {
        return number_format($this->paid_amount, 2) . ' DT';
    }
}