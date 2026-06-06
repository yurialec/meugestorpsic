<?php

namespace App\Services\Tenants;

use App\Repositories\Tenants\SubscriptionRepository;

class SubscriptionService
{
    protected $SubscriptionRepository;

    public function __construct(SubscriptionRepository $SubscriptionRepository)
    {
        $this->SubscriptionRepository = $SubscriptionRepository;
    }

    public function find()
    {
        return $this->SubscriptionRepository->find();
    }
}