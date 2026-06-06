<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'role_id' => 'required|integer',
            'password' => 'sometimes|string|min:6|regex:/[a-zA-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
            'confirmPassword' => 'required_with:password|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo Nome é obrigatório.',
            'name.string' => 'O campo Nome deve ser uma string.',
            'name.max' => 'O campo Nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'O campo E-mail deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo E-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'O E-mail informado já está em uso por outro usuário.',
            'role_id.required' => 'O campo Perfil é obrigatório.',
            'role_id.integer' => 'O campo Perfil deve ser um número inteiro.',
            'password.sometimes' => 'O campo Senha é opcional.',
            'password.string' => 'O campo Senha deve ser uma string.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra, um número e um caractere especial (@$!%*?&).',
            'confirmPassword.required_with' => 'O campo Confirmar Senha é obrigatório quando a senha é fornecida.',
            'confirmPassword.same' => 'A confirmação da senha não corresponde à senha.',
        ];
    }
}
