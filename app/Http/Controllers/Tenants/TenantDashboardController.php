<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Services\Tenants\DashboardService;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{

    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }


    public function index()
    {
        return view('tenant.dashboard.index');
    }

    public function graphsInfo()
    {
        $graphsInfo = $this->dashboardService->graphsInfo();

        if ($graphsInfo) {
            return response()->json([
                'status' => true,
                'graphsInfo' => $graphsInfo,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'erro ao localizar registro'
            ], 500);
        }
    }

    public function getAppointmentByWeek(Request $request)
    {
        $beginning_of_the_week = $request->beginning_of_the_week;
        $end_of_the_week = $request->end_of_the_week;

        $appointments = $this->dashboardService->getAppointmentByWeek($beginning_of_the_week, $end_of_the_week);

        if ($appointments) {
            return response()->json([
                'status' => true,
                'appointments' => $appointments,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'erro ao localizar registro'
            ], 500);
        }
    }
}
