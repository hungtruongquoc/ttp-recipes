<?php

namespace App\Http\Controllers;

use App\Events\CacheDataChanged;
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
use Illuminate\Support\Facades\Cache;

class RecipeController extends Controller
{
    /**
     * Cache key for all recipes.
     *
     * @var string
     */
    private const CACHE_KEY_ALL_RECIPES = 'recipes.all';

    /**
     * Get a list of all recipes with their ingredients.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse A JSON response containing a collection of recipes with ingredients
     */
    public function getRecipes(Request $request): JsonResponse
    {
        // Cache the results for 60 minutes (or any duration that makes sense for your app)
        $recipes = Cache::remember(self::CACHE_KEY_ALL_RECIPES, now()->addMinutes(60), function () {
            return Recipe::select('id', 'name', 'description', 'created_at')
                ->with('ingredients:id,recipe_id,name')
                ->latest()
                ->get();
        });

        return $this->respondWithCollection(RecipeResource::collection($recipes));
    }

    /**
     * Create a new recipe with ingredients.
     *
     * @param StoreRecipeRequest $request The validated incoming request with recipe data
     * @return JsonResponse A JSON response containing the newly created recipe resource
     * @throws RecipeCreationException If an error occurs during recipe creation
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

            event(new CacheDataChanged(self::CACHE_KEY_ALL_RECIPES));
            // Refreshes the ingredient list
            $recipe->load('ingredients');

            return $this->respondWithResource(new RecipeResource($recipe), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create recipe: ' . $e->getMessage());

            throw new RecipeCreationException('Failed to create recipe: ' . $e->getMessage(), $e);
        }
    }

    /**
     * Update an existing recipe and its ingredients.
     *
     * @param UpdateRecipeRequest $request The validated incoming request with updated recipe data
     * @param int $id The ID of the recipe to update
     * @return JsonResponse A JSON response containing the updated recipe resource
     * @throws RecipeNotFoundException If the recipe with the given ID is not found
     * @throws IngredientNotFoundException If a referenced ingredient is not found
     * @throws RecipeUpdateException If an error occurs during recipe update
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

            event(new CacheDataChanged(self::CACHE_KEY_ALL_RECIPES));
            // Refreshes the ingredient list
            $recipe->load('ingredients');

            return $this->respondWithResource(new RecipeResource($recipe));
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
