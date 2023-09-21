<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name' => ['required', 'min:3'],
            'description' => ['required', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le champ nom est réquis !',
            'name.min' => 'Le champ nom doit au moins avoir 5 caractère',
            'description.required' => 'Le champ description est réquis',
            'description.min' => 'Le champ description doit au moins avoir 5 caractère',
        ];
    }
}
