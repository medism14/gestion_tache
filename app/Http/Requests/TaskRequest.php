<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TaskDate;

class TaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required', new TaskDate],
        ];
    }

    public function messages(): array
    {
        return [
        'name.required' => 'Le champ nom est obligatoire.',
        'description.required' => 'Le champ description est obligatoire.',
        'start_date.required' => 'Le champ date de début est obligatoire.',
        'end_date.required' => 'Le champ date de fin est obligatoire.',
        ];
    }

}
