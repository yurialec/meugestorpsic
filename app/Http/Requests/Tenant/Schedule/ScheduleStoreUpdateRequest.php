<?php

namespace App\Http\Requests\Tenant\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => [
                'sometimes',
                'required',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            ],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:start_time'],

            'start_break_time' => [
                'required',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/',
                'after:start_time',
                'before:end_time',
            ],

            'end_break_time' => [
                'required',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/',
                'after:start_break_time',
                'before:end_time',
            ],
        ];
    }

    public function messages()
    {
        return [
            'day_of_week.required' => 'O dia da semana é obrigatório.',
            'day_of_week.in' => 'Dia da semana inválido.',

            'start_time.required' => 'O horário de início é obrigatório.',
            'start_time.regex' => 'Formato de horário inválido (use HH:MM ou HH:MM:SS).',

            'end_time.required' => 'O horário de término é obrigatório.',
            'end_time.regex' => 'Formato de horário inválido (use HH:MM ou HH:MM:SS).',
            'end_time.after' => 'O horário de término deve ser após o horário de início.',

            'start_break_time.required' => 'O horário de início da pausa é obrigatório.',
            'start_break_time.regex' => 'Formato de horário de início da pausa inválido (use HH:MM ou HH:MM:SS).',
            'start_break_time.after' => 'O início da pausa deve ser após o horário de início.',
            'start_break_time.before' => 'O início da pausa deve ser antes do horário de término.',

            'end_break_time.required' => 'O horário de fim da pausa é obrigatório.',
            'end_break_time.regex' => 'Formato de horário de fim da pausa inválido (use HH:MM ou HH:MM:SS).',
            'end_break_time.after' => 'O fim da pausa deve ser após o início da pausa.',
            'end_break_time.before' => 'O fim da pausa deve ser antes do horário de término.',
            'end_break_time.required_with' => 'O horário de fim da pausa é obrigatório quando o início da pausa é informado.',
        ];
    }
}