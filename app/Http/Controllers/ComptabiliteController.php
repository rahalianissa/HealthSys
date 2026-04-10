<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\WaitingRoom;
use App\Models\Invoice;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class ComptabiliteController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->middleware('auth');
        $this->middleware('role:secretaire,chef_medecine');
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('date_time', today())->count(),
            'waiting_patients' => WaitingRoom::where('status', 'waiting')->count(),
            'monthly_revenue' => Invoice::whereMonth('created_at', now()->month)->sum('amount'),
        ];
        
        $monthly_revenue_data = $this->statisticsService->getMonthlyRevenue();
        $appointments_data = $this->statisticsService->getMonthlyAppointmentsArray();
        
        $invoices = Invoice::with('patient.user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('secretaire.comptabilite', compact('stats', 'monthly_revenue_data', 'appointments_data', 'invoices'));
    }

    public function createFacture()
    {
        $patients = Patient::with('user')->get();
        return view('secretaire.create-facture', compact('patients'));
    }

    public function storeFacture(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

        Invoice::create([
            'invoice_number' => $invoiceNumber,
            'patient_id' => $request->patient_id,
            'amount' => $request->amount,
            'paid_amount' => 0,
            'status' => 'pending',
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'description' => $request->description,
        ]);

        return redirect()->to('/secretaire/comptabilite')->with('success', 'Facture créée avec succès');
    }
}