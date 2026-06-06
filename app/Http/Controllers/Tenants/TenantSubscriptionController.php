<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Services\Tenants\SubscriptionService;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    protected $SubscriptionService;

    public function __construct(SubscriptionService $SubscriptionService)
    {
        $this->SubscriptionService = $SubscriptionService;
    }

    public function index()
    {
        return view('tenant.subscription.index');
    }

    public function find()
    {
        $tenant = $this->SubscriptionService->find();

        if ($tenant) {
            return response()->json([
                'status' => true,
                'tenant' => $tenant,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'erro ao localizar registro',
            ], 500);
        }
    }
}