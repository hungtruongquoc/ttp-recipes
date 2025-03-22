<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RecipeResource extends SanitizedResource
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
            'description' => $this->sanitize($this->description),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
