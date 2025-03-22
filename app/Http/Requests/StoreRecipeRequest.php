<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreRecipeRequest extends SanitizingFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Return true by default as no authentication is required for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*' => ['required', 'string', 'max:255'],
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
            'name.required' => 'The recipe name is required.',
            'description.required' => 'The recipe description is required.',
            'ingredients.required' => 'At least one ingredient is required.',
            'ingredients.min' => 'At least one ingredient is required.',
            'ingredients.*.required' => 'Each ingredient must have a value.',
        ];
    }
}
