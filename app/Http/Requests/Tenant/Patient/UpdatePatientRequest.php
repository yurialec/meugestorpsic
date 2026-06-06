<?php

namespace App\Http\Requests\Tenant\Patient;

use App\Models\Tenants\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'group' => ['required', Rule::in(['child', 'adult', 'teen', 'elderly'])],
            'gender' => ['required', Rule::in(['F', 'M', 'other'])],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'full_name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'max:14',
                Rule::unique('patients', 'cpf')
                    ->ignore($this->id, 'id')
                    ->where(fn($query) => $query->where('tenant_id', session('tenant_id')))
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients', 'email')
                    ->ignore($this->id, 'id')
                    ->where(fn($query) => $query->where('tenant_id', session('tenant_id')))
            ],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'group.required' => 'O campo grupo é obrigatório.',
            'group.in' => 'O grupo selecionado é inválido. Valores permitidos: child, adult, teen, elderly.',
            'gender.required' => 'O campo gênero é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido. Valores permitidos: F, M, other.',
            'date_of_birth.required' => 'A data de nascimento é obrigatória.',
            'date_of_birth.date' => 'A data de nascimento deve ser uma data válida.',
            'date_of_birth.before_or_equal' => 'A data de nascimento não pode ser futura.',
            'full_name.required' => 'O campo nome completo é obrigatório.',
            'full_name.string' => 'O campo nome completo deve ser uma cadeia de caracteres.',
            'full_name.max' => 'O campo nome completo não pode ter mais de 255 caracteres.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O campo CPF deve ser uma cadeia de caracteres.',
            'cpf.max' => 'O campo CPF não pode ter mais de 14 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado neste tenant.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado neste tenant.',
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.string' => 'O campo telefone deve ser uma cadeia de caracteres.',
            'phone.max' => 'O campo telefone não pode ter mais de 20 caracteres.',
        ];
    }
}
