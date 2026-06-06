<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $SubscriptionService;

    public function __construct(SubscriptionService $SubscriptionService)
    {
        $this->SubscriptionService = $SubscriptionService;
    }

    public function index()
    {
        return view('admin.financial.subscription.index');
    }

    public function list(Request $request)
    {
        $subscriptions = $this->SubscriptionService->all();

        if ($subscriptions) {
            return response()->json([
                'status' => true,
                'subscriptions' => $subscriptions
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }
}