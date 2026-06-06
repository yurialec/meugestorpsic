<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Rules\FormatoCpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'cpf' => ['required', 'string', 'max:14', new FormatoCpfCnpj()],
            'crp' => 'required|string|digits:7|unique:employees,crp',
            'email' => 'required|email|max:255|unique:employees,email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'function' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[@$!%*?&])/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Nome
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            // CPF
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser um texto válido.',
            'cpf.max' => 'O CPF não pode ter mais de 14 caracteres.',
            'cpf.formato_cpf' => 'O CPF informado não possui um formato válido.',

            // CRP
            'crp.required' => 'O campo CRP é obrigatório.',
            'crp.integer' => 'O CRP deve conter apenas números.',
            'crp.digits' => 'O CRP deve ter exatamente 7 dígitos.',

            // Email
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail informado deve ser um endereço de e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado em nosso sistema.',

            // Telefone
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',

            // Função
            'function.required' => 'O campo função é obrigatório.',
            'function.string' => 'A função deve ser um texto válido.',
            'function.max' => 'A função não pode ter mais de 255 caracteres.',

            // Senha
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra, um número e um caractere especial (@$!%*?&).',
        ];
    }
}
