<?php

namespace App\Http\Controllers;

use App\Http\Exceptions\IngredientNotFoundException;
use App\Http\Exceptions\RecipeCreationException;
use App\Http\Exceptions\RecipeNotFoundException;
use App\Http\Exceptions\RecipeUpdateException;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;
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
        $recipes = Recipe::with('ingredients')->latest()->get();

        return response()->json(RecipeResource::collection($recipes));
    }

    /**
     * @throws RecipeCreationException
     */
    public function newRecipe(StoreRecipeRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $recipe = Recipe::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            foreach ($request->ingredients as $ingredientName) {
                $recipe->ingredients()->create([
                    'name' => $ingredientName,
                ]);
            }

            DB::commit();

            // Refreshes the ingredient list
            $recipe->load('ingredients');

            return response()->json(new RecipeResource($recipe), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create recipe: ' . $e->getMessage());

            throw new RecipeCreationException('Failed to create recipe: ' . $e->getMessage(), $e);
        }
    }

    /**
     * @throws IngredientNotFoundException
     * @throws RecipeNotFoundException
     * @throws RecipeUpdateException
     */
    public function updateRecipe(UpdateRecipeRequest $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $recipe = Recipe::findOrFail($id);

            $recipe->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Collects ids of related ingredients for removing unused ones later
            $existingIngredientIds = [];

            foreach ($request->ingredients as $ingredientData) {
                if (isset($ingredientData['id']) && $ingredientData['id'] !== null) {
                    // Update existing ingredient
                    $ingredient = Ingredient::where('id', $ingredientData['id'])
                        ->where('recipe_id', $recipe->id)
                        ->first();

                    if ($ingredient) {
                        $ingredient->update(['name' => $ingredientData['name']]);
                        $existingIngredientIds[] = $ingredient->id;
                    }
                } else {
                    $newIngredient = $recipe->ingredients()->create([
                        'name' => $ingredientData['name']
                    ]);

                    $existingIngredientIds[] = $newIngredient->id;
                }
            }

            // Deletes any ingredients not in the update request
            $recipe->ingredients()
                ->whereNotIn('id', $existingIngredientIds)
                ->delete();

            DB::commit();

            // Refreshes the ingredient list
            $recipe->load('ingredients');

            return response()->json(new RecipeResource($recipe));
        } catch (RecipeNotFoundException | IngredientNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update recipe: ' . $e->getMessage());
            Log::error('Error details: ' . $e->getTraceAsString());

            throw new RecipeUpdateException('Failed to update recipe: ' . $e->getMessage(), $e);
        }
    }
}
