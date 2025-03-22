<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UpdateRecipeRequest extends SanitizingFormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $recipeId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'ingredients' => ['present', 'array'],
            'ingredients.*.id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($recipeId) {
                    // Only validate existing ingredients belong to this recipe
                    if ($value !== null) {
                        $exists = DB::table('ingredients')
                            ->where('id', $value)
                            ->where('recipe_id', $recipeId)
                            ->exists();

                        if (!$exists) {
                            $fail('The ingredient ID does not belong to this recipe.');
                        }
                    }
                },
            ],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
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
            'ingredients.present' => 'The ingredients field must be present.',
            'ingredients.*.name.required' => 'Each ingredient must have a name.',
        ];
    }
}
