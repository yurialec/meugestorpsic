<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    public function index(): JsonResponse
    {
        try {

            $ignore = ['free', 'pending'];

            $paymentMethods = PaymentMethod::select('id', 'name', 'label')
                ->whereNotIn('name', $ignore)
                ->orderBy('id')
                ->get();
            return response()->json([
                'success' => true,
                'data' => $paymentMethods,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao listar métodos de pagamento', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao listar métodos de pagamento ativos',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
