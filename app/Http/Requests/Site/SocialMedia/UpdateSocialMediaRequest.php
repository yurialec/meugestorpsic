<?php

namespace App\Http\Requests\Site\SocialMedia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialMediaRequest extends FormRequest
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
            'name' => 'required|min:3|max:255',
            'icon' => 'required|min:3|max:255',
            'url' => 'required|min:3|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.min' => 'A nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'icon.required' => 'O campo Ícone é obrigatório.',
            'icon.min' => 'O campo Ícone deve ter no mínimo 3 caracteres.',
            'icon.max' => 'O campo Ícone não pode ter mais de 255 caracteres.',
            'url.required' => 'O campo Url é obrigatório.',
            'url.min' => 'O campo Url deve ter no mínimo 3 caracteres.',
            'url.max' => 'O campo Url não pode ter mais de 255 caracteres.',
        ];
    }
}
