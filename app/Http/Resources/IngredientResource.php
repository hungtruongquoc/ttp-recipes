<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class IngredientResource extends SanitizedResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->sanitize($this->name),
            'recipe_id' => $this->recipe_id,
        ];
    }
}
