<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link rel="stylesheet" href="{{ url('app.css') }}">
        <script src="{{ url('app.js') }}"></script>
    </head>
    <body>
        <h1 class="site-title">Retro Recipes</h1>

        <div class="container" x-data="recipesApp()">
            <div class="margin-y">
                <button class="primary" x-on:click="toggleNewRecipeForm()">New Recipe</button>
                <div class="card" x-show="newRecipe.show">
                    <fieldset>
                        <label>
                            <p>Name</p>
                            <input type="text" x-model="newRecipe.name"
                                   :class="{'field-error': newRecipe.validationErrors.name}">
                            <span class="validation-error"
                                  x-show="newRecipe.validationErrors.name"
                                  x-text="newRecipe.validationErrors.name"></span>
                        </label>
                        <label>
                            <p>Description</p>
                            <input type="text"
                                   x-model="newRecipe.description"
                                   :class="{'field-error': newRecipe.validationErrors.description}">
                            <span class="validation-error"
                                  x-show="newRecipe.validationErrors.description"
                                  x-text="newRecipe.validationErrors.description"></span>
                        </label>
                        <label>
                            <p>Ingredients</p>
                            <span class="validation-error"
                                  x-show="newRecipe.validationErrors.ingredients"
                                  x-text="newRecipe.validationErrors.ingredients"></span>
                        </label>
                        <div class="box">
                            <template x-for="(ingredient, index) in newRecipe.ingredients" :key="index">
                                <p>
                                    <input type="text" x-model="newRecipe.ingredients[index]">
                                </p>
                            </template>
                            <p>
                                <button x-on:click="newRecipe.addIngredient">Add ingredient</button>
                            </p>
                        </div>
                        <p>
                            <button class="primary"
                                    x-on:click="newRecipe.submit"
                                    x-bind:disabled="newRecipe.submitting">
                                <span x-show="!newRecipe.submitting">Save</span>
                                <span x-show="newRecipe.submitting">Saving...</span>
                            </button>
                        </p>
                    </fieldset>
                </div>
            </div>
            <div class="margin-y">
                <div x-show="loading" class="loading-text">Loading recipes...</div>

                <div x-show="!!error" class="error-text" x-cloak>
                    <pre x-text="error" style="white-space: pre-wrap; font-family: inherit;"></pre>
                </div>

                <div x-show="!loading" x-cloak>
                    <template x-for="(recipe, index) in recipes" :key="index">
                        <div class="card">
                            <div x-show="!recipe.editing">
                                <div class="flex">
                                    <h3 class="recipe-title flex-grow" x-text="recipe.data.name"></h3>
                                    <button x-on:click="startEditingExistingRecipe(recipe)">Edit</button>
                                </div>
                                <p class="recipe-description" x-text="recipe.data.description"></p>
                                <ul>
                                    <template x-for="(ingredient, index) in recipe.data.ingredients" :key="index">
                                        <li x-text="ingredient.name"></li>
                                    </template>
                                </ul>
                            </div>
                            <fieldset x-show="recipe.editing">
                                <label>
                                    <p>Name</p>
                                    <input type="text"
                                           x-model="recipe.editingData.name"
                                           :class="{'field-error': recipe.validationErrors && recipe.validationErrors.name}">
                                    <span class="validation-error"
                                          x-show="recipe.validationErrors && recipe.validationErrors.name"
                                          x-text="recipe.validationErrors && recipe.validationErrors.name"></span>
                                </label>
                                <label>
                                    <p>Description</p>
                                    <input type="text"
                                           x-model="recipe.editingData.description"
                                           :class="{'field-error': recipe.validationErrors && recipe.validationErrors.description}">
                                    <span class="validation-error"
                                          x-show="recipe.validationErrors && recipe.validationErrors.description"
                                          x-text="recipe.validationErrors && recipe.validationErrors.description"></span>
                                </label>
                                <label>
                                    <p>Ingredients</p>
                                    <span class="validation-error"
                                          x-show="recipe.validationErrors && recipe.validationErrors.ingredients"
                                          x-text="recipe.validationErrors && recipe.validationErrors.ingredients"></span>
                                </label>
                                <div class="box">
                                    <template x-for="(ingredient, index) in recipe.editingData.ingredients" :key="index">
                                        <p>
                                            <input type="text" x-model="recipe.editingData.ingredients[index].name">
                                        </p>
                                    </template>
                                    <p>
                                        <button x-on:click="addIngredientToExistingRecipe(recipe)">Add ingredient</button>
                                    </p>
                                </div>
                                <p class="flex">
                                    <button x-on:click="cancelEditing(recipe)">Cancel</button>
                                    <button class="primary"
                                            x-on:click="saveExistingRecipe(recipe)"
                                            x-bind:disabled="recipe.submitting">
                                        <span x-show="!recipe.submitting">Save</span>
                                        <span x-show="recipe.submitting">Saving...</span>
                                    </button>
                                </p>
                                <div x-show="!!recipe.error" class="error-text" x-cloak>
                                    <pre x-text="recipe.error" style="white-space: pre-wrap; font-family: inherit;"></pre>
                                </div>
                            </fieldset>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </body>
</html>
