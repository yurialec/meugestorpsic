<?php

namespace App\Http\Requests\Tenant\Consultation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'started_at' => ['required_unless:status,canceled', 'nullable', 'date_format:Y-m-d H:i:s'], // Cancelamento nao exige sessao iniciada.
            'ended_at' => ['required_unless:status,canceled', 'nullable', 'date_format:Y-m-d H:i:s', 'after:started_at'], // Cancelamento nao exige encerramento.
            'notes' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'location' => ['required', Rule::in(['online', 'in_person'])],

            'finance.payment_method_id' => [
                'required_unless:status,canceled', // Cancelamento nao deve obrigar dados financeiros.
                function ($attribute, $value, $fail) {
                    if ($this->input('status') === 'canceled' && empty($value)) {
                        return; // Cancelamento nao envia pagamento.
                    }
                    if (!is_int($value) && !in_array($value, ['pending', 'free'], true)) {
                        $fail("O campo {$attribute} deve ser um número inteiro ou 'pending'/'free'.");
                    }
                },
            ],

            'finance.payment_status' => [
                'required_unless:status,canceled', // Cancelamento nao cria nem valida pagamento.
                Rule::in(['paid', 'pending', 'free']),
            ],

            'finance.payment_amount' => [
                'required_if:finance.payment_status,!=,free',
                'nullable',
                'numeric',
                'min:0',
            ],

            'finance.discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value > 0) {
                        $paymentAmount = $this->input('finance.payment_amount') ?? 0;
                        if ((float) $paymentAmount <= 0) {
                            $fail("O {$attribute} só pode ser aplicado se o valor do pagamento for maior que zero.");
                        }
                    }
                },
            ],

            'finance.credit_type' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $methodId = $this->input('finance.payment_method_id');
                    $status = $this->input('finance.payment_status');

                    if ($methodId == 3 && $status === 'paid') {
                        if (empty($value)) {
                            $fail("O campo {$attribute} é obrigatório para pagamentos com cartão de crédito efetivados.");
                        } elseif (!in_array($value, ['vista', 'parcelado'], true)) {
                            $fail("O campo {$attribute} deve ser 'vista' ou 'parcelado'.");
                        }
                    } else {
                        if (!empty($value)) {
                            $fail("O campo {$attribute} só é permitido para pagamentos com cartão de crédito efetivados.");
                        }
                    }
                },
            ],

            'finance.installments' => [
                'nullable',
                'integer',
                'min:2',
                function ($attribute, $value, $fail) {
                    $creditType = $this->input('finance.credit_type');
                    $status = $this->input('finance.payment_status');

                    if ($status === 'paid' && $creditType === 'parcelado') {
                        if (empty($value)) {
                            $fail("O campo {$attribute} é obrigatório para pagamentos parcelados efetivados.");
                        }
                    } else {
                        if (!empty($value)) {
                            $fail("O campo {$attribute} só é permitido para pagamentos parcelados efetivados.");
                        }
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'started_at.required' => 'A data de início da consulta é obrigatória.',
            'started_at.date_format' => 'A data de início deve estar no formato YYYY-MM-DD HH:mm:ss.',

            'ended_at.required' => 'A data de término da consulta é obrigatória.',
            'ended_at.date_format' => 'A data de término deve estar no formato YYYY-MM-DD HH:mm:ss.',
            'ended_at.after' => 'A data de término deve ser posterior à data de início.',

            'location.required' => 'O local da consulta é obrigatório.',
            'location.in' => 'O local da consulta deve ser "online" ou "in_person".',

            'finance.payment_status.required' => 'O status do pagamento é obrigatório.',
            'finance.payment_status.in' => 'O status do pagamento deve ser "paid", "pending" ou "free".',

            'finance.payment_amount.required_if' => 'O valor do pagamento é obrigatório, exceto para consultas gratuitas.',
            'finance.payment_amount.numeric' => 'O valor do pagamento deve ser um número válido.',
            'finance.payment_amount.min' => 'O valor do pagamento não pode ser negativo.',

            'finance.discount.numeric' => 'O desconto deve ser um valor numérico.',
            'finance.discount.min' => 'O desconto não pode ser negativo.',

            'finance.installments.integer' => 'O número de parcelas deve ser um número inteiro.',
            'finance.installments.min' => 'O número de parcelas deve ser no mínimo 2 para pagamentos parcelados.',
        ];
    }
}
