<?php

namespace App\Interfaces\Tenants;

interface DashboardRepositoryInterface
{
    public function graphsInfo();
    public function getAppointmentByWeek($beginning_of_the_week, $end_of_the_week);
}