<?php

namespace Tests\Feature\Http\Controllers;

use App\Events\CacheDataChanged;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving recipes with cache and proper JSON structure.
     */
    public function test_get_recipes_returns_recipes_in_expected_structure()
    {
        // Create a recipe with an ingredient
        $recipe = Recipe::factory()->create([
            'name' => 'Test Recipe',
            'description' => 'A delicious test recipe'
        ]);
        $recipe->ingredients()->create([
            'name' => 'Salt'
        ]);

        // Optionally, fake the cache to ensure our closure is executed
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(Recipe::with('ingredients')->latest()->get());

        $response = $this->getJson('/api/recipes');
//        $response->dump();
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'ingredients' => [
                        '*' => [
                            'id',
                            'recipe_id',
                            'name'
                        ]
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test creating a new recipe and dispatching the cache invalidation event.
     */
    public function test_new_recipe_creates_recipe_and_dispatches_event()
    {
        // Fake event dispatching
        Event::fake();

        $payload = [
            'name' => 'New Recipe',
            'description' => 'A brand new recipe',
            'ingredients' => ['Flour', 'Water']
        ];

        $response = $this->postJson('/api/recipes', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'id',
                    'name',
                    'description',
                    'ingredients',
                    'created_at'
                ]);

        // Assert that the recipe exists in the database
        $this->assertDatabaseHas('recipes', [
            'name' => 'New Recipe',
            'description' => 'A brand new recipe'
        ]);

        // Assert that ingredients were created (should be 2)
        $this->assertDatabaseCount('ingredients', 2);

        // Assert that the cache invalidation event was dispatched
        Event::assertDispatched(CacheDataChanged::class);
    }
}
