<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $recipes = [
            [
                'name' => 'Chicken à la King',
                'description' => 'A classic creamy chicken dish from the 1950s, perfect for a cozy dinner.',
                'ingredients' => [
                    '2 cups cooked chicken, diced',
                    '1/2 cup green bell pepper, chopped',
                    '1/2 cup mushrooms, sliced',
                    '1 cup cream of mushroom soup',
                    '1/2 cup frozen peas',
                ],
            ],
            [
                'name' => 'Tuna Casserole',
                'description' => 'A retro, hearty casserole, ideal for a simple weeknight meal.',
                'ingredients' => [
                    '1 can tuna, drained',
                    '1 cup egg noodles, cooked',
                    '1 cup peas',
                    '1 can cream of celery soup',
                    '1/2 cup breadcrumbs',
                ],
            ],
            [
                'name' => 'Beef Wellington',
                'description' => 'An elegant, show-stopping dish with tender beef wrapped in flaky pastry.',
                'ingredients' => [
                    '1 lb beef tenderloin',
                    '1/2 cup pâté',
                    '1/2 lb mushrooms, finely chopped',
                    '1 package puff pastry',
                    '1 egg (for egg wash)',
                ],
            ],
            [
                'name' => 'Jell-O Salad',
                'description' => 'A sweet and tangy gelatin salad, perfect for retro parties.',
                'ingredients' => [
                    '1 box lime Jell-O',
                    '1 cup cottage cheese',
                    '1/2 cup crushed pineapple, drained',
                    '1/2 cup chopped walnuts',
                ],
            ],
            [
                'name' => 'Pineapple Upside-Down Cake',
                'description' => 'A vintage dessert with caramelized pineapple and a buttery, moist cake.',
                'ingredients' => [
                    '1 can pineapple rings',
                    '1/4 cup brown sugar',
                    '1/2 cup butter',
                    '1 cup all-purpose flour',
                    '1/2 cup sugar',
                ],
            ],
            [
                'name' => 'Stuffed Bell Peppers',
                'description' => 'A hearty meal with ground beef, rice, and tomato sauce in a bell pepper shell.',
                'ingredients' => [
                    '4 bell peppers, halved',
                    '1 lb ground beef',
                    '1 cup cooked rice',
                    '1/2 cup tomato sauce',
                    '1/4 cup grated cheese',
                ],
            ],
            [
                'name' => 'Classic Meatloaf',
                'description' => 'A comforting, nostalgic meatloaf recipe with a savory flavor and simple ingredients.',
                'ingredients' => [
                    '1 lb ground beef',
                    '1 onion, chopped',
                    '1 cup breadcrumbs',
                    '1 egg',
                    '1/4 cup ketchup',
                ],
            ],
            [
                'name' => 'Shrimp Cocktail',
                'description' => 'A retro appetizer of chilled shrimp served with zesty cocktail sauce.',
                'ingredients' => [
                    '1 lb cooked shrimp, peeled',
                    '1 cup cocktail sauce',
                    '1 lemon, sliced',
                    'Ice for serving',
                ],
            ],
            [
                'name' => 'Creamed Spinach',
                'description' => 'A rich and creamy spinach side dish, often served at retro holiday dinners.',
                'ingredients' => [
                    '1 lb spinach, chopped',
                    '1/2 cup heavy cream',
                    '2 tbsp butter',
                    '1/4 cup grated parmesan',
                    '1/4 tsp nutmeg',
                ],
            ],
            [
                'name' => 'Beef Stroganoff',
                'description' => 'A creamy, savory beef dish served over egg noodles, a 1960s classic.',
                'ingredients' => [
                    '1 lb beef sirloin, sliced thin',
                    '1 onion, chopped',
                    '1 cup sour cream',
                    '1 cup beef broth',
                    '2 tbsp flour',
                ],
            ],
        ];

        // Loop through the recipe data
        foreach ($recipes as $recipeData) {
            // Create the recipe
            $recipe = Recipe::create([
                'name' => $recipeData['name'],
                'description' => $recipeData['description'],
            ]);

            foreach ($recipeData['ingredients'] as $ingredient) {
                $recipe->ingredients()->create([
                    'name' => $ingredient,
                ]);
            }
        }
    }
}
