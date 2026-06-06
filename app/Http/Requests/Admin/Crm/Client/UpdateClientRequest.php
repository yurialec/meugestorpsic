<?php

namespace App\Http\Requests\Admin\Crm\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('id');
        return [
            'cpf' => 'required|string|max:14|unique:clients,cpf,' . $clientId,
            'domain' => 'required|string|min:7|max:7',
            'email' => 'required|email|max:255|unique:clients,email,' . $clientId,
            'function' => 'required',
            'name' => 'required',
            'phone' => 'required|string|max:20',
            'plan_id' => 'required',
            'password' => 'nullable|string|min:6|regex:/[a-zA-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser uma string válida.',
            'cpf.max' => 'O CPF não pode ter mais de 14 caracteres.',
            'cpf.unique' => 'O CPF informado já está em uso.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail informado deve ser um e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'O e-mail informado já está em uso.',
            'plan.required' => 'O campo plano é obrigatório.',
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser uma string válida.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'function.required' => 'O campo função é obrigatório.',
            'domain.required' => 'O campo domínio é obrigatório.',
            'domain.string' => 'O domínio deve ser uma string.',
            'domain.min' => 'O domínio deve ter exatamente 7 caracteres.',
            'domain.max' => 'O domínio não pode ter mais de 7 caracteres.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra, um número e um caractere especial.',
        ];
    }
}
