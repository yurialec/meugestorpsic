<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PaymentsService;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    protected $PaymentsService;

    public function __construct(PaymentsService $PaymentsService)
    {
        $this->PaymentsService = $PaymentsService;
    }

    public function index()
    {
        return view('admin.financial.payments.index');
    }

    public function list()
    {
        $payments = $this->PaymentsService->all();

        if ($payments) {
            return response()->json([
                'status' => true,
                'payments' => $payments,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Nenhum pagamento encontrado.',
        ], 500);
    }
}
