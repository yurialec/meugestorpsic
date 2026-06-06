<?php

namespace App\Services\Tenants;

use App\Repositories\Tenants\DashboardRepository;


class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function graphsInfo()
    {
        return $this->dashboardRepository->graphsInfo();
    }

    public function getAppointmentByWeek($beginning_of_the_week, $end_of_the_week)
    {
        return $this->dashboardRepository->getAppointmentByWeek($beginning_of_the_week, $end_of_the_week);
    }
}