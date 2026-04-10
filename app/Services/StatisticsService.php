<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Doctor;
use Carbon\Carbon;

class StatisticsService
{
    public function getGlobalStats(): array
    {
        return [
            'total_patients' => Patient::count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_secretaries' => User::where('role', 'secretaire')->count(),
            'total_appointments' => Appointment::count(),
            'total_revenue' => Invoice::sum('amount'),
            'total_paid' => Invoice::sum('paid_amount'),
            'pending_payment' => Invoice::sum('amount') - Invoice::sum('paid_amount'),
        ];
    }

    public function getTodayAppointments()
    {
        return Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('date_time', today())
            ->orderBy('date_time')
            ->get();
    }

    public function getMonthlyAppointmentsArray(): array
    {
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = Appointment::whereMonth('date_time', $i)
                ->whereYear('date_time', date('Y'))
                ->count();
        }
        return $data;
    }

    public function getMonthlyRevenue(): array
    {
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = Invoice::whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('amount');
        }
        return $data;
    }

    public function getRecentPatients(int $limit = 5)
    {
        return Patient::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentDoctors(int $limit = 5)
    {
        return User::with('specialite')
            ->where('role', 'doctor')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopDoctors(int $limit = 5)
    {
        return Doctor::with('user')
            ->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getChartData(): array
    {
        $months = [];
        $appointments = [];
        $revenues = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create(null, $i, 1)->translatedFormat('F');
            $appointments[] = Appointment::whereMonth('date_time', $i)
                ->whereYear('date_time', date('Y'))
                ->count();
            $revenues[] = Invoice::whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('amount');
        }

        return compact('months', 'appointments', 'revenues');
    }
}