<?php

namespace App\Http\Requests\Admin\Crm\Status;

use Illuminate\Foundation\Http\FormRequest;

class CreateStatusRequest extends FormRequest
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
            'name' => 'required|max:255|unique:crm_statuses,name',
            'color' => 'required|max:255|unique:crm_statuses,color',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um status com este nome.',
            'color.required' => 'O campo cor é obrigatório.',
            'color.string' => 'A cor deve ser uma string válida.',
            'color.max' => 'A cor não pode ter mais de 255 caracteres.',
            'color.unique' => 'Já existe um status com esta cor.',
        ];
    }
}
