<?php

namespace App\Http\Requests\Tenant\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
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
            'group' => 'required|in:child,adult,teen,elderly',
            'gender' => 'required|in:F,M,other',
            'full_name' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'max:14',
                Rule::unique('patients')->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'));
                })
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients')->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'));
                })
            ],
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today|after:1900-01-01',
        ];
    }

    public function messages(): array
    {
        return [
            'group.required' => 'O campo grupo é obrigatório.',
            'group.in' => 'O grupo selecionado é inválido. Valores permitidos: child, adult, teen, elderly.',
            'gender.required' => 'O campo gênero é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido. Valores permitidos: F, M, other.',
            'full_name.required' => 'O campo nome completo é obrigatório.',
            'full_name.string' => 'O campo nome completo deve ser uma cadeia de caracteres.',
            'full_name.max' => 'O campo nome completo não pode ter mais de 255 caracteres.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O campo CPF deve ser uma cadeia de caracteres.',
            'cpf.max' => 'O campo CPF não pode ter mais de 14 caracteres.',
            'cpf.unique' => 'Já existe um paciente com este CPF cadastrado em sua clínica.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Já existe um paciente com este e-mail cadastrado em sua clínica.',
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.string' => 'O campo telefone deve ser uma cadeia de caracteres.',
            'phone.max' => 'O campo telefone não pode ter mais de 20 caracteres.',
            'date_of_birth.required' => 'O campo data de nascimento é obrigatório.',
            'date_of_birth.date' => 'O campo data de nascimento deve ser uma data válida.',
            'date_of_birth.before' => 'A data de nascimento não pode ser futura.',
            'date_of_birth.after' => 'A data de nascimento deve ser posterior a 1900.',
        ];
    }
}