<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::put('/recipes/{id}', [RecipeController::class, 'updateRecipe']);
Route::get('/recipes', [RecipeController::class, 'getRecipes']);
Route::post('/recipes', [RecipeController::class, 'newRecipe']);
