<?php

namespace App\Http\Requests\Admin\Crm\Client;

use App\Rules\FormatoCpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
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
            'cpf' => ['required', 'string', 'max:18', new FormatoCpfCnpj()],
            'crp' => 'required|string|unique:clients,crp',
            'email' => 'required|email:rfc,dns|max:255|unique:clients,email',
            'domain' => 'required|string|max:50|unique:tenant,domain',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
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

            // Email
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail informado deve ser um endereço de e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado em nosso sistema.',

            // Domain
            'domain.required' => 'O campo domínio é obrigatório.',
            'domain.string' => 'O CPF deve ser um texto válido.',
            'domain.max' => 'O domínio não pode ter mais de 50 caracteres.',
            'domain.unique' => 'Este domínio já está cadastrado em nosso sistema.',

            // Telefone
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',

            // Senha
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra, um número e um caractere especial (@$!%*?&).',
        ];
    }
}
