<?php

namespace App\Services\Admin;

use App\Repositories\Admin\SubscriptionRepository;

class SubscriptionService
{
    protected $SubscriptionRepository;

    public function __construct(SubscriptionRepository $SubscriptionRepository)
    {
        $this->SubscriptionRepository = $SubscriptionRepository;
    }

    public function all()
    {
        return $this->SubscriptionRepository->all();
    }
}