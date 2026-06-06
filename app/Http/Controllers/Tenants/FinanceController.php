<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Services\Tenants\FinanceService;
use Illuminate\Http\Request;
use Validator;

class FinanceController extends Controller
{
    protected $FinanceService;

    public function __construct(FinanceService $FinanceService)
    {
        $this->FinanceService = $FinanceService;
    }

    public function index()
    {
        return view('tenant.finance.index');
    }

    public function list(Request $request)
    {
        $receipts = $this->FinanceService->all($request->all());

        if ($receipts) {
            return response()->json([
                'status' => true,
                'receipts' => $receipts
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function getPdf(Request $request)
    {
        $pdf = $this->FinanceService->getPdf($request->all());

        if ($pdf) {
            return $pdf->download('teste.pdf');
        } else {
            return response()->json([
                'message' => 'Erro ao gera pdf.',
                'status' => 500
            ]);
        }
    }

    public function billsToReciveIndex()
    {
        return view('tenant.finance.billstorecive.index');
    }

    public function billsToPayIndex(): string
    {
        return 'contas a pagar';
    }

    public function billsToReciveList(Request $request)
    {
        $recive = $this->FinanceService->billsToReciveList($request->all());

        if ($recive) {
            return response()->json([
                'status' => true,
                'recive' => $recive
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => 500
            ]);
        }
    }

    public function registerPayment($tenant, $id, Request $request)
    {
        $data = $request->payLoad;

        $validator = Validator::make($data, [
            'payment_method_id' => 'required|integer|in:1,2,3,4,5',
            'discount' => 'nullable|numeric|min:0',
            'credit_type' => [
                'nullable',
                function ($attribute, $value, $fail) use ($data) {
                    $methodId = $data['payment_method_id'] ?? null;

                    if ($methodId == 3) {
                        if (empty($value)) {
                            $fail("O campo {$attribute} é obrigatório para pagamentos com cartão de crédito.");
                        } elseif (!in_array($value, ['vista', 'parcelado'], true)) {
                            $fail("O campo {$attribute} deve ser 'vista' ou 'parcelado'.");
                        }
                    } else {
                        if (!empty($value)) {
                            $fail("O campo {$attribute} só é permitido para pagamentos com cartão de crédito.");
                        }
                    }
                },
            ],
            'installments' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
                function ($attribute, $value, $fail) use ($data) {
                    $creditType = $data['credit_type'] ?? null;
                    if ($creditType === 'parcelado') {
                        if (empty($value)) {
                            $fail("O campo {$attribute} é obrigatório quando o tipo de crédito é 'parcelado'.");
                        }
                    } else {
                        if (!empty($value)) {
                            $fail("O campo {$attribute} só é permitido quando o tipo de crédito é 'parcelado'.");
                        }
                    }
                },
            ],
        ], [
            'payment_method_id.required' => 'O método de pagamento é obrigatório.',
            'payment_method_id.integer' => 'O método de pagamento deve ser um número inteiro.',
            'payment_method_id.in' => 'O método de pagamento selecionado é inválido.',

            'discount.numeric' => 'O desconto deve ser um valor numérico.',
            'discount.min' => 'O desconto não pode ser negativo.',

            'installments.integer' => 'O número de parcelas deve ser um número inteiro.',
            'installments.min' => 'O número mínimo de parcelas é 1.',
            'installments.max' => 'O número máximo de parcelas é 12.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dados de pagamento inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $recive = $this->FinanceService->registerPayment($id, $data);

        if ($recive) {
            return response()->json([
                'status' => true,
                'recive' => $recive
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nenhum registro encontrado.',
                'status' => false,
            ], 500);
        }
    }
}