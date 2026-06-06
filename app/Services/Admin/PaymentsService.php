<?php

namespace App\Services\Admin;

use App\Repositories\Admin\PaymentsRepository;

class PaymentsService
{
    protected $PaymentsRepository;

    public function __construct(PaymentsRepository $PaymentsRepository)
    {
        $this->PaymentsRepository = $PaymentsRepository;
    }

    public function all()
    {
        return $this->PaymentsRepository->all();
    }
}