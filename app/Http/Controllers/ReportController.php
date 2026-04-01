<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    
    public function index()
    {
        return view('reports.index');
    }

    public function monthly(Request $request)
    {
        $month = $request->month ? Carbon::parse($request->month) : Carbon::now();
        
        $appointments = Appointment::whereYear('date_time', $month->year)
            ->whereMonth('date_time', $month->month)
            ->get();
            
        $invoices = Invoice::whereYear('issue_date', $month->year)
            ->whereMonth('issue_date', $month->month)
            ->get();
            
        $newPatients = Patient::whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();
            
        $totalRevenue = $invoices->sum('amount');
        $totalPaid = $invoices->sum('paid_amount');
        
        $stats = [
            'month' => $month->format('F Y'),
            'appointments_count' => $appointments->count(),
            'confirmed_appointments' => $appointments->where('status', 'confirmed')->count(),
            'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'new_patients' => $newPatients,
            'total_revenue' => $totalRevenue,
            'total_paid' => $totalPaid,
            'pending_payment' => $totalRevenue - $totalPaid,
            'appointments_by_type' => [
                'general' => $appointments->where('type', 'general')->count(),
                'emergency' => $appointments->where('type', 'emergency')->count(),
                'follow_up' => $appointments->where('type', 'follow_up')->count(),
                'specialist' => $appointments->where('type', 'specialist')->count(),
            ]
        ];
        
        return view('reports.monthly', compact('stats', 'month'));
    }

    public function yearly(Request $request)
    {
        $year = $request->year ?? date('Y');
        
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $appointments = Appointment::whereYear('date_time', $year)
                ->whereMonth('date_time', $i)
                ->count();
                
            $revenue = Invoice::whereYear('issue_date', $year)
                ->whereMonth('issue_date', $i)
                ->sum('amount');
                
            $monthlyData[] = [
                'month' => Carbon::create($year, $i, 1)->format('F'),
                'appointments' => $appointments,
                'revenue' => $revenue
            ];
        }
        
        $totalAppointments = Appointment::whereYear('date_time', $year)->count();
        $totalPatients = Patient::whereYear('created_at', $year)->count();
        $totalRevenue = Invoice::whereYear('issue_date', $year)->sum('amount');
        $totalPaid = Invoice::whereYear('issue_date', $year)->sum('paid_amount');
        
        $stats = [
            'year' => $year,
            'total_appointments' => $totalAppointments,
            'total_patients' => $totalPatients,
            'total_revenue' => $totalRevenue,
            'total_paid' => $totalPaid,
            'pending_payment' => $totalRevenue - $totalPaid,
            'monthly_data' => $monthlyData
        ];
        
        return view('reports.yearly', compact('stats', 'year'));
    }
    
    public function export(Request $request)
    {
        $type = $request->type;
        $format = $request->format ?? 'pdf';
        
        if ($type == 'monthly') {
            $month = $request->month ? Carbon::parse($request->month) : Carbon::now();
            $appointments = Appointment::with(['patient.user', 'doctor.user'])
                ->whereYear('date_time', $month->year)
                ->whereMonth('date_time', $month->month)
                ->get();
                
            $data = [
                'title' => 'Rapport mensuel - ' . $month->format('F Y'),
                'appointments' => $appointments,
                'month' => $month
            ];
            
            if ($format == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.export-pdf', $data);
                return $pdf->download('rapport_' . $month->format('Y-m') . '.pdf');
            }
        }
        
        return back()->with('success', 'Export généré avec succès');
    }
}