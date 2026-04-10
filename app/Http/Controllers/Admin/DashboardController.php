<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->middleware('auth');
        $this->middleware('role:chef_medecine');
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        $stats = $this->statisticsService->getGlobalStats();
        $todayAppointments = $this->statisticsService->getTodayAppointments();
        $monthlyAppointments = $this->statisticsService->getMonthlyAppointmentsArray();
        $recentPatients = $this->statisticsService->getRecentPatients();
        $recentDoctors = $this->statisticsService->getRecentDoctors();
        $topDoctors = $this->statisticsService->getTopDoctors();

        return view('admin.dashboard', compact(
            'stats',
            'todayAppointments',
            'monthlyAppointments',
            'recentPatients',
            'recentDoctors',
            'topDoctors'
        ));
    }

    public function getChartData()
    {
        return response()->json($this->statisticsService->getChartData());
    }
}