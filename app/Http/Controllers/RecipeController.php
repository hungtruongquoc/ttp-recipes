<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $name = $request->input('name');
        $description = $request->input('description');
        $ingredientNames = $request->input('ingredients');

        // Use a transaction to ensure atomicity
        try {
            DB::beginTransaction();

            $recipe = new Recipe();
            $recipe->name = $name;
            $recipe->description = $description;
            $recipe->save();

            foreach ($ingredientNames as $ingredientName) {
                $ingredient = new Ingredient();
                $ingredient->name = $ingredientName;
                $recipe->ingredients()->save($ingredient);
            }

            DB::commit();

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
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Failed to create recipe: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to create recipe'
            ], 500);
        }
    }
}
