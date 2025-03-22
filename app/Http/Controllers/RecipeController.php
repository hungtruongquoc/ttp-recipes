<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function getRecipes(Request $request): JsonResponse
    {
        $recipes = Recipe::get();
    
        $formattedRecipes = $recipes->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description,
                'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                    ];
                }),
            ];
        });
    
        return response()->json($formattedRecipes);
    }

    public function newRecipe(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $ingredientNames = json_decode($request->get('ingredients'));
    
        $recipe = new Recipe();
        $recipe->name = $name;
        $recipe->description = $description;
        $recipe->save();
    
        foreach ($ingredientNames as $ingredientName) {
            $ingredient = new Ingredient();
            $ingredient->name = $ingredientName;
            $recipe->ingredients()->save($ingredient);
        }
    
        return response()->json([
            'id' => $recipe->id,
            'name' => $recipe->name,
            'description' => $recipe->description,
            'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                ];
            }),
        ]);
    }
}
