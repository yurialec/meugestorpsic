<?php

namespace App\Http\Requests\Admin\Crm\Interaction;

use Illuminate\Foundation\Http\FormRequest;

class CreateInsteractionRequest extends FormRequest
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
            'status_id' => 'required|exists:crm_statuses,id',
            'observation' => 'sometimes|min:3|max:1000',
            'attachment' => 'sometimes|file|mimes:jpeg,png,pdf|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status_id.required' => 'O campo status é obrigatório.',
            'status_id.exists' => 'O status selecionado não é válido.',
            'observation.min' => 'A observação deve ter pelo menos 3 caracteres.',
            'observation.max' => 'A observação não pode exceder 1000 caracteres.',
            'attachment.file' => 'O arquivo enviado deve ser um arquivo válido.',
            'attachment.mimes' => 'O arquivo deve ser do tipo JPEG, PNG ou PDF.',
            'attachment.max' => 'O tamanho máximo permitido para o arquivo é 2MB.',
        ];
    }
}
