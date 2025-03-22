<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
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
        $recipes = Recipe::with('ingredients')->get();

        return response()->json($recipes);
    }

    public function newRecipe(StoreRecipeRequest $request): JsonResponse
    {
        // Use a transaction to ensure atomicity
        try {
            DB::beginTransaction();

            // Create the recipe with validated data
            $recipe = Recipe::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Add ingredients
            foreach ($request->ingredients as $ingredientName) {
                $recipe->ingredients()->create([
                    'name' => $ingredientName,
                ]);
            }

            DB::commit();

            // Load the ingredients relationship before returning
            $recipe->load('ingredients');

            return response()->json($recipe, 201);
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
